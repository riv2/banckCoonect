<?php

namespace App\Http\Controllers\Student;


use App\CreditPrice;
use App\FinanceNomenclature;
use App\Lecture;
use App\PayDocument;
use App\PayDocumentStudentDiscipline;
use App\Rules\Student\FreeSeats;
use App\Semester;
use App\Services\MirasApi;
use App\Services\Service1C;
use App\StudentFinanceNomenclature;
use App\StudentLecture;
use App\StudentSubmodule;
use App\Validators\ProfileStudyAjaxBuyServiceValidator;
use App\User;
use App\Wifi;
use App\WifiTariff;
use Dosarkz\EPayKazCom\Facades\Epay;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\StudentDiscipline;
use App\Profiles;
use Illuminate\Validation\Rule;
use Auth;
use App\Http\Controllers\Controller;
use App\Services\{FinanceNomenclatureService, PayCloudService};
use mysql_xdevapi\Exception;
use PHPUnit\Runner\AfterIncompleteTestHook;
use Carbon\Carbon;

class PayController extends Controller
{
    /**
     * Buying discipline
     *
     * @param Request $request
     * @param int $disciplineId discipline ID
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \Exception
     */
    public function pay(Request $request, int $disciplineId)
    {
        // Buying not allowed
        if (!Auth::user()->studentProfile->buying_allow) {
            $this->flash_danger('Purchase denied');
            return redirect()->route('study');
        }

        $credits = abs((int)$request->input('credits', null));

        $SDId = StudentDiscipline::getId(Auth::user()->id, $disciplineId);

        // Link does not exist
        if (empty($SDId)) {
            // Discipline in submodule. TODO in this submodule
            if (!empty($request->input('submodule_id')) && StudentSubmodule::studentHasDiscipline(Auth::user()->id, $disciplineId)) {
                $SDId = StudentSubmodule::addDisciplineRelations(
                    Auth::user()->id,
                    $disciplineId,
                    $request->input('submodule_id'),
                    Auth::user()->studentProfile->education_speciality_id
                );
            } else {
                $this->flash_danger('StudentDiscipline does not exist.');
                return redirect()->route('study');
            }
        }

        $SD = StudentDiscipline::getDisciplineForPay(Auth::user()->id, $SDId);

        if (empty($SD) || empty($SD->discipline)) {
            $this->flash_danger('StudentDiscipline does not exist.');
            return redirect()->route('study');
        }

        if ($SD->pay_processing) {
            $this->flash_danger('The payment process is not completed. Repeat in a few minutes.');
            return redirect()->route('study');
        }

        $SD->setBuyAvailable(Auth::user());

        if (!$SD->buyAvailable) {
            $this->flash_danger('Purchase is not available.');
            return redirect()->route('study');
        }

        // Full buying
        if (empty($credits)) {
            $credits = $SD->getCreditsForFullBuy();
        }
        // Partial buy
        else {
            $credits = $SD->getCreditsForPartialBuy($credits);
        }

        if ($SD->migrated_type == StudentDiscipline::MIGRATED_TYPE_NOT_FREE) {
            $semester = Semester::current(Auth::user()->studentProfile->education_study_form);
        } else {
            $semester = $SD->plan_semester;
        }

        $amount = $credits * Auth::user()->getCreditPrice($semester);

        if ($amount > Auth::user()->balanceByDebt()) {
            return redirect()->back()->withErrors(__('Not enough funds on balance'));
        }

        $payDocument = PayDocument::createForStudentDiscipline(
            Auth::user()->id,
            $this->getOrderId($SD->id),
            $amount,
            $credits,
            $SDId,
            Auth::user()->balance
        );

        if (empty($payDocument)) {
            abort(500, 'Cannot create PayDocument.');
        }

        // Pay processing ON
        StudentDiscipline::setPayProcessing($SDId, true);

        $paySuccess = Service1C::payDiscipline(Auth::user()->studentProfile->iin, $amount, $payDocument);

        if ($paySuccess) {
            $payDocument->changePayStatus(true, null);

            // Pay processing OFF
            StudentDiscipline::setPayProcessing($SDId, false);

            return view('pay.success', compact('payDocument'));
        } else {
            $payDocument->changePayStatus(false, null);

            // Pay processing OFF
            StudentDiscipline::setPayProcessing($SDId, false);

            return view('pay.fail', compact('payDocument'));
        }
    }

    /**
     * Buying Service
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function buyService(Request $request)
    {
        $return = [
            'status' => true,
            'message' => __('Success buy service')
        ];

        // validation data
        $obValidator = ProfileStudyAjaxBuyServiceValidator::make($request->all());
        if ($obValidator->fails()) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        $response = FinanceNomenclatureService::buy($request->input('service'));

        return $response;

    }

    /**
     * @param Request $request
     */
    public function payToBalance(Request $request)
    {
        Log::info($request->all());

        $userId = $request->input('AccountId', '');

        if(!$userId)
        {
            abort(404);
        }

        $user = User::where('id', $userId)->first();
        $token = $request->input('Token');

        if(!$user || !$token)
        {
            abort(400);
        }

        /*if($user->id != 10556)
        {
            abort(404);
        }*/

        $orderId = $request->input('InvoiceId');
        $amount = $request->input('Amount');
        $totalFee = $request->input('TotalFee');
        $totalAmount = $amount - $totalFee;
        $status = $request->input('Status');
        $iin = $user->studentProfile->iin ?? '999999999999';
        $data = json_decode($request->input('Data'), true);
        $saveCard = (isset($data['saveCard']) && $data['saveCard'] === true) ? true : false;

        if($status == 'Completed') {

            if($saveCard)
            {
                $user->attachPayCard(
                    $request->input('CardFirstSix'),
                    $request->input('CardLastFour'),
                    $request->input('CardType'),
                    $request->input('CardExpDate'),
                    $request->input('Issuer'),
                    $request->input('IssuerBankCountry'),
                    $request->input('Token')
                );
            }

            $addBalanceResult = Service1C::addToBalance(
                $iin,
                $totalAmount,
                Service1C::BANK_NAME_SBER,
                Service1C::getBankDayAfterToday()
                );
            if(!$addBalanceResult)
            {
                throw new \Exception('1c Add balance error');
            }
        }

        PayDocument::createForBalance([
            'order_id'              => $orderId,
            'user_id'               => $user->id,
            'amount'                => $totalAmount,
            'complete_pay'          => true,
            'hash'                  => json_encode($request->all()),
            'status'                => $status == 'Completed' ? PayDocument::STATUS_SUCCESS : PayDocument::STATUS_FAIL,
            'type'                  => PayDocument::TYPE_TO_BALANCE
        ]);

        return Response::json(["code" => 0]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function payToBalanceByToken(Request $request)
    {
        /*if(\App\Services\Auth::user()->id != 10556)
        {
            abort(404);
        }*/

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'card_id' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json([
                'status' => 'error',
                'message' => $validator->messages()
            ]);
        }

        $user = \App\Services\Auth::user();
        $payCard = $user->payCards()->where('id', $request->input('card_id'))->first();

        if( !($payCard && $payCard->token) )
        {
            return Response::json([
                'status' => 'error',
                'message' => __('There was an error while paying')
            ]);
        }

        $amount = $request->input('amount');
        $invoiceId = $user->studentProfile->iin . rand(1000, 9999);
        $payCloud = new PayCloudService($payCard->token);
        $payResult = $payCloud->pay($amount, $user->id, $invoiceId, __('Deposit balance in miras.app'));

        Log::info($payResult);

        $payResult = $payResult['Success'] ?? false;
        $resultAnswer = [
            'status' => 'error',
            'message' => __('There was an error while paying')
        ];

        if($payResult)
        {
            $resultAnswer = [
                'status' => 'success'
            ];
        }

        return Response::json($resultAnswer);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function removeCard(Request $request)
    {
        /*if(\App\Services\Auth::user()->id != 10556)
        {
            abort(404);
        }*/

        $validator = Validator::make($request->all(), [
            'card_id' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json([
                'status' => 'error',
                'message' => $validator->messages()
            ]);
        }

        $user = \App\Services\Auth::user();
        $payCard = $user->payCards()->where('id', $request->input('card_id'))->first();

        if($payCard)
        {
            $payCard->delete();
            return Response::json(['status' => 'success']);
        }

        return Response::json([
            'status' => 'error',
            'message' => __('Card not found')
        ]);
    }

    public function payWifi(Request $request, $tariffId)
    {
        $tariff = WifiTariff::where('id', $tariffId)->first();

        if(!$tariff)
        {
            abort(404);
        }

        //if(!Auth::user()->studentProfile->iin) {

            /*By Epay*/

            $orderId = '999999999999' . rand(1000, 9999);

            $pay = Epay::basicAuth([
                'order_id' => $orderId,
                'currency' => '398',
                'amount' => $tariff->cost,
                //'email' => Auth::user()->email ?? '',
                'hashed' => true,
                'back_link' => url('pay/result?id=' . $orderId)
            ]);

            PayDocument::createForWifi([
                'order_id' => $orderId,
                'user_id' => Auth::user()->id,
                'amount' => $tariff->cost,
                'status' => PayDocument::STATUS_PROCESS,
                'type' => PayDocument::TYPE_WIFI,
                'tariff_id' => $tariff->id
            ]);

            return redirect($pay->generateUrl());
        /*}
        else
        {
            //By balance

            if(\App\Services\Auth::user()->balance < $tariff->cost)
            {
                return redirect()->back()->withErrors('Not enough funds on balance');
            }

            $nomenclature = '';
            if(Service1C::pay(\App\Services\Auth::user()->iin, $nomenclature, $tariff->cost))
            {
                $wifi = new Wifi();
                $wifi->user_id = Auth::user()->id;
                $wifi->code = str_random(10);
                $wifi->value = $tariff->value;
                $wifi->status = Wifi::STATUS_ACTIVE;
                $wifi->save();

                return redirect()->back();
            }
        }*/


    }

    public function payRetakeTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:students_disciplines,id'
        ]);

        if ($validator->fails()) {
            return view('pay.order')->withErrors($validator->errors());
        }

        $discipline = StudentDiscipline::getDisciplineForPay(Auth::user()->id, $request->input('id'));
        $orderId = $this->getOrderId($discipline->id);
        $amount = config('app.retakeTestPrice');

        $created = PayDocument::createForStudentRetakeTest(Auth::user()->id, $orderId, $amount, $request->input('id'));

        if ($created) {
            $pay = $this->getEPay($orderId, $amount);
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function payRetakeKge(Request $request)
    {
        $amount = config('app.retakeKgePrice');

        $orderId = time() . rand(10000, 100000);

        $payDocument = PayDocument::createForStudentRetakeKge(Auth::user()->id, $orderId, $amount);

        if (!empty($payDocument)) {
            $pay = $this->getEPay($orderId, $amount);
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function payLecture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => [
                'required',
                'exists:lectures,id',
                new FreeSeats($request->input('type'))
            ],
            'type' => [
                'required',
                Rule::in(
                    StudentLecture::TYPE_ONLINE,
                    StudentLecture::TYPE_OFFLINE
                )]
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $lecture = Lecture::where('id', $request->input('id'))->first();
        $orderId = $this->getOrderId($lecture->id);
        $amount = $lecture->cost;

        $created = PayDocument::createForLecture(Auth::user()->id, $orderId, $amount, $request->input('type'), $request->input('id'));

        if ($created) {
            $pay = $this->getEPay($orderId, $amount);
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * @param Request $request
     * @return PayController|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function payLectureRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:lectures,id'
        ]);

        if ($validator->fails()) {
            return view('pay.order')->withErrors($validator->errors());
        }

        $lecture = Lecture::where('id', $request->input('id'))->first();
        $orderId = time() . $lecture->id . rand(1, 200);

        $reserve = MirasApi::request(MirasApi::ROOM_RESERVE_INFO, [
            'id' => $lecture->room_booking_id
        ]);

        if (!$reserve) {
            abort(500);
        }

        $created = PayDocument::createForLectureRoom(Auth::user()->id, $orderId, $reserve->cost, $lecture->id);

        if ($created) {
            $pay = $this->getEPay($orderId, $reserve->cost);
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payTestGet(Request $request)
    {
        return view('pay.order');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payToBalanceForm(Request $request)
    {
        /*if(\App\Services\Auth::user()->id != 10556)
        {
            abort(404);
        }*/

        return view('pay.to_balance');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function payTestPost(Request $request)
    {
        $orderId = time();
        $amount = $request->input('amount');

        $created = PayDocument::createForTest(Auth::user()->id, $orderId, $amount);

        if ($created) {
            $pay = $this->getEPay($orderId, $amount);
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * Get auth answer from epay
     *
     * @param Request $request
     * @return int
     */
    public function authResultSuccess(Request $request)
    {
        $response = request()->input('response');

        if ($response) {
            $payResponse = Epay::handleBasicAuth($response);

            $orderId = $payResponse->getOrderId();
            $payDocument = PayDocument::where('order_id', $orderId)->first();

            $paySuccess = $payResponse->isSuccess(['amount' => $payDocument->amount]);
            $payStatus = $payDocument->changePayStatus($paySuccess, $payResponse->getResponse());

            /*Complete pay*/
            if ($paySuccess && $payDocument->complete_pay == false) {
                $completePay = Epay::controlPay([
                    'order_id' => $orderId,
                    'amount' => $payResponse->getAmount(),
                    'approval_code' => $payResponse->getApprovalCode(),
                    'reference' => $payResponse->getReference(),
                    'currency' => '398',
                    'command_type' => 'complete', //reverse || complete || refund
                    'reason' => 'auto complete pay'
                ]);

                $completeResponse = Epay::request($completePay->generateUrl());

                if ($completeResponse) {
                    $controlPayResponse = Epay::handleControlPay($completeResponse);
                    Log::info('Complete pay', [
                        'status' => $controlPayResponse->isSuccess() ? 'success' : 'fail',
                        'message' => $controlPayResponse->getResponseMessage(),
                        'response' => $controlPayResponse->getResponse()
                    ]);

                    if ($controlPayResponse->isSuccess()) {
                        $payDocument->complete_pay = true;
                        $payDocument->save();
                    }
                }
            }
            /*---*/

            Log::info('Pay order', [
                'order_id' => $payResponse->getOrderId(),
                'pay status' => $payStatus,
                'epay_response' => $payResponse->getResponse()
            ]);
        }

        return 0;
    }

    /**
     * @param Request $request
     * @return int
     */
    public function authResultFail(Request $request)
    {
        $response = request()->input('response');

        if ($response) {
            preg_match('|order_id="[0-9]+"|', $response, $matches);
            $orderId = $matches[0] ? str_replace(['order_id=', '"'], '', $matches[0]) : 0;
            if (!$orderId) {
                return 0;
            }

            $payDocument = PayDocument::where('order_id', $orderId)->first();

            if (!$payDocument) {
                return 0;
            }

            $checkPay = Epay::checkPay(['order_id' => $orderId]);
            $response = Epay::request($checkPay->generateUrl());

            if ($response) {
                $checkPayResponse = Epay::handleCheckPay($response);

                $payStatus = $payDocument->changePayStatus($checkPayResponse->isSuccess(),
                    $checkPayResponse->getResponse());

                Log::info('Pay order', [
                    'order_id' => $orderId,
                    'pay status' => $payStatus,
                    'epay_response' => $checkPayResponse->getResponse(),
                    'epay_fail' => $response
                ]);
            }
        }

        return 0;
    }

    /**
     * Action for user pay callback
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function backLink(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pay_documents,order_id'
        ]);

        if ($validator->fails())
        {
            abort(404);
        }

        $orderId = $request->input('id');
        $payDocument = PayDocument::where('order_id', $orderId)->first();

        $checkPay = Epay::checkPay(['order_id' => $orderId]);
        $response = Epay::request($checkPay->generateUrl());

        if ($response) {
            $checkPayResponse = Epay::handleCheckPay($response);

            $payStatus = $payDocument->changePayStatus($checkPayResponse->isSuccess(), $checkPayResponse->getResponse());

            Log::info('Pay order(backlink)', [
                'order_id' => $orderId,
                'pay status' => $payStatus,
                'epay_response' => $checkPayResponse->getResponse()
            ]);

            if ($checkPayResponse->isSuccess()) {
                /*Complete pay*/
                if ($payDocument->complete_pay == false) {
                    $completePay = Epay::controlPay([
                        'order_id' => $request->input('id'),
                        'amount' => $checkPayResponse->getAmount(),
                        'approval_code' => $checkPayResponse->getApprovalCode(),
                        'reference' => $checkPayResponse->getReference(),
                        'currency' => '398',
                        'command_type' => 'complete', //reverse || complete || refund
                        'reason' => 'auto complete pay'
                    ]);

                    $completeResponse = Epay::request($completePay->generateUrl());

                    if ($completeResponse) {
                        $controlPayResponse = Epay::handleControlPay($completeResponse);
                        Log::info('Complete pay (backlink)', [
                            'status' => $controlPayResponse->isSuccess() ? 'success' : 'fail',
                            'message' => $controlPayResponse->getResponseMessage(),
                            'response' => $controlPayResponse->getResponse()
                        ]);

                        if ($controlPayResponse->isSuccess()) {
                            $payDocument->complete_pay = true;
                            $payDocument->save();
                        }
                    }
                }
                /*---*/

                return view('pay.success', ['payDocument' => $payDocument]);
            }
        }

        return view('pay.fail', ['payDocument' => $payDocument]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function payRegistrationFee(Request $request)
    {
        $profile = Profiles::select(['paid', 'id'])->where('user_id', Auth::user()->id)->first();

        if ($profile->paid == 1) {
            return redirect()->route('financesPanel');
        }

        $orderId = time() . $profile->id;
        $amount = config('app.registrationFee');

        $backLinkParams = ['id' => $orderId];

        if ($request->input('back', '')) {
            $backLinkParams['back'] = $request->input('back');
        }

        $created = PayDocument::createForProfile(Auth::user()->id, $orderId, $amount);

        if ($created) {
            $pay = $this->getEPay($orderId, $amount, url('pay/regfee/result?' . http_build_query($backLinkParams)));
            return redirect($pay->generateUrl());
        }

        abort(500, 'Cannot create PayDocument.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function backLinkProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:pay_documents,order_id'
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $back = $request->input('back', '');
        $back = $back ? base64_decode($back) : '';

        $orderId = $request->input('id');
        $payDocument = PayDocument::where('order_id', $orderId)
            ->where('user_id', Auth::user()->id)
            ->first();

        if (!$payDocument) {
            abort(404);
        }

        $checkPay = Epay::checkPay(['order_id' => $orderId]);
        $response = Epay::request($checkPay->generateUrl());

        if ($response) {
            $checkPayResponse = Epay::handleCheckPay($response);

            $payStatus = $payDocument->changePayStatus($checkPayResponse->isSuccess(), $checkPayResponse->getResponse());

            if ($payStatus == 'success') {
                $profile = Profiles::where('user_id', Auth::user()->id)->first();
                $profile->paid = 1;
                $profile->save();
            }

            Log::info('Pay order(backlink)', [
                'order_id' => $orderId,
                'pay status' => $payStatus,
                'payDocument' => $payDocument,
                'epay_response' => $checkPayResponse->getResponse()
            ]);

            if ($checkPayResponse->isSuccess()) {
                /*Complete pay*/
                if (!$payDocument->complete_pay) {
                    $completePay = Epay::controlPay([
                        'order_id' => $request->input('id'),
                        'amount' => $checkPayResponse->getAmount(),
                        'approval_code' => $checkPayResponse->getApprovalCode(),
                        'reference' => $checkPayResponse->getReference(),
                        'currency' => '398',
                        'command_type' => 'complete', //reverse || complete || refund
                        'reason' => 'auto complete pay'
                    ]);

                    $completeResponse = Epay::request($completePay->generateUrl());

                    if ($completeResponse) {
                        $controlPayResponse = Epay::handleControlPay($completeResponse);
                        Log::info('Complete pay (backlink)', [
                            'status' => $controlPayResponse->isSuccess() ? 'success' : 'fail',
                            'message' => $controlPayResponse->getResponseMessage(),
                            'response' => $controlPayResponse->getResponse()
                        ]);

                        if ($controlPayResponse->isSuccess()) {
                            $payDocument->complete_pay = true;
                            $payDocument->save();
                        }
                    }
                }
                /*---*/

                return view('pay.success', ['payDocument' => $payDocument, 'back' => $back]);
            }
        }

        return view('pay.fail', ['payDocument' => $payDocument, 'back' => $back]);
    }

    private function getOrderId(int $id)
    {
        return time() . $id;
    }

    private function getEPay(string $orderId, $amount, $backLink = null, int $currency = 398, $email = null, $hashed = true)
    {
        $email = $email ?? Auth::user()->email;
        $backLink = $backLink ?? url('pay/result?id=' . $orderId);

        return Epay::basicAuth([
            'order_id' => $orderId,
            'currency' => $currency,
            'amount' => $amount,
            'email' => $email,
            'hashed' => $hashed,
            'back_link' => $backLink
        ]);
    }

    /**
     * @param int $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function test1Trial(int $disciplineId, Request $request)
    {
        $SD = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($SD)) {
            abort(404);
        }

        // Not Test1 Retake time
        if (!Auth::user()->isTest1RetakeTime($SD->plan_semester)) {
            $this->flash_danger('Error. It is not Test1 retake time now.');
            return redirect()->route('study');
        }

        // Result already is trial
        if ($SD->test1_result_trial) {
            return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
        }

        // FIXME на время эпидемии
        if ($SD->corona_distant || $SD->remote_access) {
            // Test1 time && Has free attempts - to SelectMethod
            if (Auth::user()->isTest1Time($SD->plan_semester) && $SD->hasTest1FreeAttemptCorona()) {
                return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
            }
        } else {
            // Test1 time
            if (Auth::user()->isTest1Time($SD->plan_semester) && $SD->hasTest1FreeAttempt()) {
                return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
            }
        }

        $financeNomenclature = FinanceNomenclature::getTest1Trial();

        // Low balance
        if (Auth::user()->balance < $financeNomenclature->cost) {
            return redirect()->route('studentTest1Trial', ['id' => $disciplineId]);
        }

        $balanceBeforeCall = Auth::user()->balance;

        $mResponse = Service1C::pay(
            Auth::user()->studentProfile->iin,
            $financeNomenclature->code,
            $financeNomenclature->cost
        );

        // Successfully paid
        if ($mResponse) {
            // Add log
            StudentFinanceNomenclature::addTest1Trial(
                Auth::user()->id,
                $financeNomenclature,
                Auth::user()->studentProfile->currentSemester(),
                $balanceBeforeCall,
                $SD->id
            );

            // Set trial
            $SD->setTest1Trial();

            // TODO before Service1C::pay()
            // Add Pay Document
            PayDocument::createTest1Trial(Auth::user()->id, $financeNomenclature->cost, $SD->id, $balanceBeforeCall);
        }

        return redirect()->route('studentSelectTest1Method', ['id' => $disciplineId]);
    }

    /**
     * @param int $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function remoteAccess(int $disciplineId, Request $request)
    {
        if ($request->session()->has('remote_access_redirect')) {
            $redirect = $request->session()->pull('remote_access_redirect');
        } else {
            $redirect = ['route' => 'study', 'params' => []];
        }

        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        // Not exists
        if (empty($studentDiscipline)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        // Practice
        if ($studentDiscipline->discipline->is_practice) {
            $this->flash_danger('You cannot buy remote access for practice');
            return redirect()->route('study');
        }

        // Already remote
        if ($studentDiscipline->remote_access || Auth::user()->free_remote_access) {
            return redirect()->route($redirect['route'], $redirect['params']);
        }

        // Processing
        if ($studentDiscipline->pay_processing) {
            $this->flash_info('Payment is not available, because the transaction is in processing. Refresh the page in a few seconds.');
            return redirect()->route('study');
        }

        $priceFor1Credit = Auth::user()->remote_access_price;

        // Price is empty
        if (empty($priceFor1Credit)) {
            $this->flash_danger('An error has occurred. Please contact the Registration Office by phone +77750073000.');
            return redirect()->route('study');
        }

        $financeNomenclature = FinanceNomenclature::getRemoteAccess($studentDiscipline->discipline->ects, $priceFor1Credit);

        // Low balance
        if (false && Auth::user()->balanceByDebt() < $financeNomenclature->cost) {
            return redirect()->route('remoteAccessPay', ['id' => $disciplineId]);
        }

        // Pay processing ON
        StudentDiscipline::setPayProcessing($studentDiscipline->id, true);

        $balanceBeforeCall = Auth::user()->balance;

        $success = Service1C::pay(Auth::user()->studentProfile->iin, $financeNomenclature->code, $financeNomenclature->cost);

        // Successfully paid
        if ($success) {
            // Add log
            StudentFinanceNomenclature::addRemoteAccess(
                Auth::user()->id,
                $financeNomenclature,
                Auth::user()->studentProfile->currentSemester(),
                $balanceBeforeCall,
                $studentDiscipline->id
            );

            $studentDiscipline->setRemoteAccess();

            // Pay processing OFF
            StudentDiscipline::setPayProcessing($studentDiscipline->id, false);

            // TODO Добавить PayDocument?

            $this->flash_success('Remote access purchased');

            return redirect()->route($redirect['route'], $redirect['params']);
        } else {
            // Pay processing OFF
            StudentDiscipline::setPayProcessing($studentDiscipline->id, false);

            $this->flash_danger('An error occurred while paying');

            return redirect()->route($redirect['route'], $redirect['params']);
        }
    }

    /**
     * @param int $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function examTrial(int $disciplineId, Request $request)
    {
        $studentsDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentsDiscipline)) {
            abort(404);
        }

        // Result already is trial
        if ($studentsDiscipline->test_result_trial) {
            return redirect()->route('studentSelectExamMethod', ['id' => $disciplineId]);
        }

        $financeNomenclature = FinanceNomenclature::getExamTrial();

        // Low balance
        if (Auth::user()->balance < $financeNomenclature->cost) {
            return redirect()->route('studentExamTrial', ['id' => $disciplineId]);
        }

        $balanceBeforeCall = Auth::user()->balance;

        $mResponse = Service1C::pay(
            Auth::user()->studentProfile->iin,
            $financeNomenclature->code,
            $financeNomenclature->cost
        );

        // Successfully paid
        if ($mResponse) {
            // Add log
            StudentFinanceNomenclature::addExamTrial(
                Auth::user()->id,
                $financeNomenclature,
                Auth::user()->studentProfile->currentSemester(),
                $balanceBeforeCall,
                $studentsDiscipline->id
            );

            // Set trial
            $studentsDiscipline->setExamTrial();

            // TODO Before Service1C:pay()
            // Add Pay Document
            PayDocument::createExamTrial(Auth::user()->id, $financeNomenclature->cost, $studentsDiscipline->id, $balanceBeforeCall);
        }

        return redirect()->route('studentSelectExamMethod', ['id' => $disciplineId]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payTestCloudpay()
    {
        return view('pay.test_cloudpay');
    }

    /**
     * @param Request $request
     */
    public function payResultTestCloudpay(Request $request)
    {
        Log::info($request->all());
    }
}