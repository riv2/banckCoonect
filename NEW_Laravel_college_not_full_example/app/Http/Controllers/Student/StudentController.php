<?php

namespace App\Http\Controllers\Student;

use App\Discipline;
use App\StudentSubmodule;
use App\Submodule;
use App\Mail\PracticePayedMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\User;
use App\StudentDiscipline;
use App\DiscountCategoryList;
use App\DiscountTypeList;
use App\Profiles;
use App\Bank;
use App\Refund;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Intervention\Image\Facades\Image;
use App\DiscountStudent;
use App\ProfileDoc;
use App\Validators\{StudentAjaxGetTransactionHistoryValidator, SyllabusTaskCoursePayPayValidator};
use App\Services\{TransactionService};
use App\Services\Service1C;
use App\Services\SmsService;
use App\{DisciplinesPracticePay, DisciplineSubmodule, FinanceNomenclature, Language, Semester, SyllabusTaskCoursePay, TransactionHistory};
use App\DocsEnquire;
use Validator;

class StudentController extends Controller
{
    const MIN_CREDITS_AT_SEMESTER_TO_REFUND = 15;

    public static $studyForms = [
        Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
        Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Онлайн',
        Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
        Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
    ];

    public function finances()
    {
        $user = Auth::user();
        $balance = $user->balance ?? 0;
        $profile = Profiles::where('user_id', $user->id)->first();

        $categories = DiscountCategoryList::get();
        foreach ($categories as $category) {
            if(\Lang::locale() != 'ru') {
                $category->name = $category->{'name_' . \Lang::locale()};
            }
            
        }
        $discountTypes = DiscountTypeList::get();

        $discountCount = DiscountStudent
            ::where('user_id', Auth::user()->id)
            ->where(function ($query) {
                $query->where('status', DiscountStudent::STATUS_APPROVED);
            })
            ->count();

        $discountHistory = DiscountStudent
            ::select('discount_type_list.*', 'discount_student.*')
            ->where('user_id', $user->id)
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->get();

        $banks = Bank::get();

        $refunds = Refund::select('refunds_list.*', 'banks_list.name as bank', 'docs_enquire.filename as doc')
            ->where('refunds_list.user_id', $user->id)
            ->leftJoin('banks_list', 'banks_list.id', 'refunds_list.bank_id')
            ->leftJoin('docs_enquire', 'docs_enquire.id', 'refunds_list.doc_id')
            ->orderBy('created_at','desc')
            ->get();
        $refundReferencePaid = 'false';
        $refundSmsSent = 'false';
        $filename = 'null';
        $lastIban = null;
        $lastBankId = null;
        foreach ($refunds as $refund) {
            if($refund->status == Refund::STATUS_REFERENCE) {
                $refundReferencePaid = 'true';
                $filename = DocsEnquire::where('id', $refund->doc_id)->first();
                $filename = "'". url(DocsController::DOCS_FOLDER . $filename->filename). "'";
                if(isset($refund->sms_key)) {
                    $refundSmsSent = 'true';
                }
            }
            if($refund->status == Refund::STATUS_RETURNED) {
                $refund->status = $refund->bank_comment;
            }
            if(isset($refund->doc)) {
                $refund->doc = url(DocsController::DOCS_FOLDER . $refund->doc);
            }
            if( $lastIban == null && $refund->status == Refund::STATUS_BANK_PROCESSING ) {
                $lastIban = $refund->user_iban;
                $lastBankId = $refund->bank_id;
            }
        }
        
        return view('student.finance', compact(
            'balance',
            'profile',
            'categories',
            'discountTypes',
            'discountCount',
            'discountHistory',
            'banks',
            'refundReferencePaid',
            'refundSmsSent', 
            'refunds',
            'filename',
            'lastIban',
            'lastBankId'
        ));
    }

    public function refundCheck($enough)
    {
        $user = Auth::user();
        ///check is it student by iin
        if(!isset($user->studentProfile->iin) || $user->studentProfile->alien == 1) {
            return [
                'status'  => 'fail',
                'message' => __('You have to be a student')
            ];
        }
        if(Service1C::getBalance($user->studentProfile->iin) < $enough) {
            return [
                'status'  => 'fail',
                'message' => __('You do not have enough balance')
            ];
        }

        //check minimum 15 credits in current semestr
        $payedCredits = StudentDiscipline::getPayedCreditSumAtCurrentSemester($user->id);
        if ($payedCredits < self::MIN_CREDITS_AT_SEMESTER_TO_REFUND ) {
            return [
                'status'  => 'fail',
                'message' => __('Minimum credits in current semester allowed to refund is') . ' ' . self::MIN_CREDITS_AT_SEMESTER_TO_REFUND
            ];
        }
        
        return ['status' => 'success'];
    }

    public function refundReferencePay(Request $request)
    {   
        $user = Auth::user();
        
        $takeMoney = $this->refundCheck(Refund::REFUND_SIZE + Refund::REFERENCE_PRICE);
        if ($takeMoney['status'] == 'fail') {
            return $takeMoney;
        }

        // checking if user have processing refund (in other window|tab)
        $existsProcessingOrder = Refund::where('user_id', $user->id)
                ->where(function($q) {
                    $q->where('status', Refund::STATUS_REFERENCE)
                        ->orWhere('status', Refund::STATUS_PROCESSING);
                })->exists();

        if ( $existsProcessingOrder ) {
            return [
                'status'  => 'fail',
                'message' => __('Your previous application has not yet been processed, please complete it before')
            ];
        }

        $nomenclature = "00000003274";
        $payResult = Service1C::pay(Auth::user()->studentProfile->iin, $nomenclature, Refund::REFERENCE_PRICE);
        if(!$payResult) {
            return [
                'status'  => 'fail',
                'message' => __('Payment error')
            ];
        }

        $genDocs = DocsController::genRefundReference();
        //generate reference and return link
        $refund = new Refund;
        $refund->user_id = Auth::user()->id;
        $refund->doc_id = $genDocs['enquireId'];
        $refund->save();

        return [
            'status' => 'success',
            'filename' => $genDocs['filename']
        ];
    }

    public function refundRequest(Request $request)
    {
        $user = Auth::user();
        
        $data = \Input::except(['_token']);

        $rule = [
            'bankIban' => 'required|min:18|max:18',
            'bankId'   => 'required'
        ];

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return [
                'status'  => 'fail',
                'message' => __('IBAN number error')
            ];
        }
        
        $input = $request->all();
        
        $bank = Bank::where('id', $input['bankId'])->first();

        $refund = Refund::where('user_id', $user->id)
            ->where('status', Refund::STATUS_REFERENCE)
            ->first();

        if(isset($refund->id)) {
            // generate sms code
            $smsKey = substr(uniqid('', true), -4);
            $refund->user_iban = $input['bankIban'];
            $refund->bank_id = $input['bankId'];
            $refund->sms_key = $smsKey;
            $refund->save();
        }

        //send sms
        if ( isset($user->studentProfile->mobile) ) {
            SmsService::send($user->studentProfile->mobile, 'MirasEducation code: ' . $smsKey);
        }

        return ['status'  => 'success'];
    }

    public function refundSmsCode(Request $request)
    {
        $user = Auth::user();
        $takeMoney = $this->refundCheck(Refund::REFUND_SIZE);
        if($takeMoney['status'] == 'fail') {
            return $takeMoney;
        }

        $data = \Input::except(['_token']);

        $rule = [
            'sms_code' => 'required|min:4|max:4'
        ];

        $validator = \Validator::make($data, $rule);
        $input = $request->all();

        $refund = Refund::where('user_id', $user->id)
            ->where('status', Refund::STATUS_REFERENCE)
            ->first();

        if($validator->fails() || $input['sms_code'] != $refund->sms_key) {
            $refund->sms_key = null;
            $refund->save();
            return [
                'status'  => 'fail',
                'message' => __('Wrong SMS code')
            ];
        }

        $bank = Bank::where('id', $refund->bank_id)->first();

        $params1c = [
            "iin"      => $user->studentProfile->iin,
            "cost"     => strval($refund->tiyn/100).".00",
            "bank"     => $bank->name, 
            "bik_bank" => $bank->bic,
            "iban"     => Refund::IBAN_KZ.$refund->user_iban
        ];
        if (!env('API_1C_ENABLED', false) || env('API_1C_EMULATED', false)) {
            $refundResult = true;
        } else {
            $refundResult = Service1C::sendRequest(Service1C::API_REFUND, $params1c);
        
            if (!$refundResult) {
                return [
                    'status'  => 'fail',
                    'message' => __('Refund error')
                ];
            }
        }

        if(isset($refund->id)) {
            $refund->status = Refund::STATUS_PROCESSING;
            $refund->save();
        }
        //$bank = Bank::where('id', $refund->bank_id)->first();

        Mail::send('emails.refund_request', [
            'refund' => $refund,
            'user' => $user,
            'bank' => $bank
            ],
            function ($message) use ($refund, $user, $bank) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( explode(',',env('EMAIL_BUH_REFUND_INFORM')) )
                    ->subject('Заявление на возврат от '. $user->studentProfile->fio .' '. $user->studentProfile->iin);
        });

        return [
            'status'  => 'success',
            'message' => __('Thank you! A request has been sent to processing')
        ];
    }

    /**
     * Adding discount request
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function discountPost(Request $request)
    {
        $discountCount = DiscountStudent
            ::where('user_id', Auth::user()->id)
            ->where(function ($query) {
                $query->where('status', 'new');
            })
            ->count();

        if ($discountCount > 0) {
            return redirect()->back()->withErrors([__('You already sent request')]);
        }

        for ($i = 1; $i < 20; $i++) {
            if ($request->hasFile('image' . $i)) {
                $image = $request->file('image' . $i);
                //uploading file and adding it to DB
                ProfileDoc::saveDocument(ProfileDoc::TYPE_DISCOUNT_PROOF, $image);
            } else {
                break;
            }
        }

        $discount = new DiscountStudent;
        $discount->type_id = $request->input('type_id');
        $discount->user_id = Auth::user()->id;
        $discount->status = DiscountStudent::STATUS_NEW;
        $discount->save();

        DiscountStudent::addToAdminSearchCache($discount, Auth::user()->studentProfile->fio);

        \Session::flash('flash_message', __('Your discount request has been sent. Processing will take 3 business days'));
        return redirect()->back();
    }

    /**
     * Full buying page
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function disciplinePay(Request $request, int $id)
    {
        // Buying not allowed
        if (!Auth::user()->studentProfile->buying_allow) {
            $this->flash_danger('Purchase denied');
            return redirect()->route('study');
        }

        $SD = StudentDiscipline::getOne(Auth::user()->id, $id);

        // Discipline does not exist in student's list
        if (empty($SD)) {
            // This discipline in his submodule. TODO in this submodule
            if (StudentSubmodule::studentHasDiscipline(Auth::user()->id, $id)) {
                $SD = StudentDiscipline::getForPay(Auth::user()->id, $id);
            } else {
                abort(404);
            }
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

        $SD->discipline;

        // TODO check for dependencies

        $credits = $SD->getCreditsForFullBuy();

        if ($SD->migrated_type == StudentDiscipline::MIGRATED_TYPE_NOT_FREE) {
            $semester = Semester::current(Auth::user()->studentProfile->education_study_form);
        } else {
            $semester = $SD->plan_semester;
        }

        $creditPrice = Auth::user()->getCreditPrice($semester);

        $submoduleId = $request->input('submodule_id');

        $vars = compact('SD', 'creditPrice', 'credits', 'submoduleId');

        if (Auth::user()->balanceByDebt() < $creditPrice * $credits) {
            return view('student.disciplinePay', $vars)->withErrors(['balance' =>  [__('Not enough funds on balance')]]);
        }

    	return view('student.disciplinePay', $vars);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function disciplinePartialPay(Request $request, int $id)
    {
        // Buying not allowed
        if (!Auth::user()->studentProfile->buying_allow) {
            $this->flash_danger('Purchase denied');
            return redirect()->route('study');
        }

        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $id);

        // Discipline does not exist in student's list
        if (empty($studentDiscipline)) {
            // This discipline in his submodule. TODO in this submodule
            if (StudentSubmodule::studentHasDiscipline(Auth::user()->id, $id)) {
                $studentDiscipline = StudentDiscipline::getForPay(Auth::user()->id, $id);
            } else {
                abort(404);
            }
        }

        if ($studentDiscipline->pay_processing) {
            $this->flash_danger('The payment process is not completed. Repeat in a few minutes.');
            return redirect()->route('study');
        }

        $studentDiscipline->setBuyAvailable(Auth::user());

        if (!$studentDiscipline->buyAvailable) {
            $this->flash_danger('Purchase is not available.');
            return redirect()->route('study');
        }

        $studentDiscipline->discipline;

        // TODO check for dependencies

        $maxCredits = $studentDiscipline->getCreditsForFullBuy();

        $creditPrice = Auth::user()->getCreditPrice($studentDiscipline->plan_semester);
        $userBalance = Auth::user()->balanceByDebt();

        $submoduleId = $request->input('submodule_id');

    	return view('student.disciplinePartialPay', compact('studentDiscipline', 'creditPrice', 'maxCredits', 'submoduleId', 'userBalance'));
    }

    public function miningPay($discipline_id)
    {
        $miningpayed = DisciplinesPracticePay::
        where('discipline_id', $discipline_id)->
        where('user_id', Auth::user()->id)->
        first();
        $iCost = 4000;

        if (empty($miningpayed)){
            $miningNotPayed = true;
        }else{
            $miningNotPayed = false;
        }
        return view('student.syllabustaskcoursepay.miningPay', array(
            'cost'           => $iCost,
            'discipline_id'  => $discipline_id,
            'miningNotPayed' => $miningNotPayed,
        ));
    }

    public function miningPayPost(Request $request)
    {
        $obValidator = SyllabusTaskCoursePayPayValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()->studentProfile) )
        {
            return redirect()->route('study')->withErrors([__('Data not found')]);
        }

        // cost
        $iCost = 4000;

        // test user balance
        if( $iCost > Auth::user()->balanceByDebt() )
        {
            return redirect()->route('study')->withErrors([__('Not enough funds on balance')]);
        }

        $bResponse = Service1C::pay(
            Auth::user()->studentProfile->iin,
            '00000004145',
            $iCost
        );
        $disciplinesPayed = DisciplinesPracticePay::where('user_id', Auth::user()->id)
            ->where('discipline_id', $request->input('discipline_id'))
            ->count();

        if( !empty($bResponse) and $disciplinesPayed === 0)
        {

            $disciplinesPracticePay = new DisciplinesPracticePay();
            $disciplinesPracticePay->fill([
                'discipline_id' => $request->input('discipline_id'),
                'user_id'       => Auth::user()->id,
                'status'        => 'process',
                'payed_sum'     => 4000
            ]);
            $disciplinesPracticePay->save();

            $student = Auth::user();
            $data = [
                'id' => $student->id,
                'name' => $student->name,
                'discipline' => Discipline::find($request->input('discipline_id'))->name,
                'speciality' => $student->studentProfile->speciality->name,
                'study_form' => self::$studyForms[$student->studentProfile->education_study_form],
                'lang' => $student->studentProfile->education_lang,
                'payed_sum' => $disciplinesPracticePay->payed_sum,
                'date' => $disciplinesPracticePay->created_at,
                'group' =>  $student->studentProfile->team
            ];
            Mail::to('miras.praktika@inbox.ru')->send(new PracticePayedMail($data));

            \Session::put('withoutBack',true);
            return redirect()->route('study')->with('flash_message',__('Already payed'));

        }


        return redirect()->route('study')->withErrors([__('Error input data')]);
    }

    /*
        public function balancePay(Request $request, $id)
        {
            return view('student.disciplinePay', compact('discipline', 'creditPrice', 'credits'));
        }
    */

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function retakePay(Request $request)
    {
        $discipline = StudentDiscipline::select('students_disciplines.*', 'disciplines.name', 'disciplines.ects as credits')
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', Auth::user()->id)
            ->where('students_disciplines.id', $request->id)
            ->first();

        $creditPrice = config('app.retakeTestPrice');


        return view('student.retakePay', compact('discipline', 'creditPrice'));
    }

    public function retakeKgePay(Request $request)
    {
        $creditPrice = config('app.retakeKgePrice');

        return view('student.retakeKgePay', compact('creditPrice'));
    }

    public function analogue(Request $request)
    {
        $discipline = StudentDiscipline::select()
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', Auth::user()->id)
            ->where('students_disciplines.id', $request->id)
            ->first();

        return view('student.analogue', compact('discipline'));
    }

    public function analoguePost(Request $request)
    {
        $emailOR = 'office_reg@miras.edu.kz';

        $data = \Input::except(array('_token'));

        $inputs = $request->all();

        $rule = ['certificate' => 'required'];
        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        /** @var StudentDiscipline $studentDiscipline */
        $studentDiscipline = StudentDiscipline::where('students_disciplines.discipline_id', $request->id)->first();

        // start certificate //
        $tmpFilePath = 'images/uploads/analogue/';
        $image = $request->file('certificate');
        $fileName = md5(str_random(15));
        $img = Image::make($image);

        $img->fit(1350, 963)->save($tmpFilePath . $fileName . '-b.jpg', 100);
        $img->fit(450, 321)->save($tmpFilePath . $fileName . '-s.jpg', 100);

        $studentDiscipline->analogue = $fileName;
        // end certificate //

        if (!empty($inputs['notes'])) {
            $studentDiscipline->notes = $inputs['notes'];
        }

        $studentDiscipline->save();

        \Session::flash('flash_message', __('Thank you for sending certificate we will inform you as soon it will be checked.'));

        $bigImagePath = $tmpFilePath . $fileName . '-b.jpg';

        Mail::send('emails.analogue', [
            'studentDiscipline' => $studentDiscipline,
            'user' => Auth::user()
            ],
            function ($message) use ($emailOR, $studentDiscipline, $bigImagePath) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to($emailOR)
                    ->subject('Перезачёт "'. $studentDiscipline->discipline->name .'" для '. Auth::user()->studentProfile->fio)
                    ->attach($bigImagePath);
            });

        return redirect()->route('financesPanel');
    }

    public function syllabus($idkey)

    {
        //did studend bought this discipline
        $discipline = StudentDiscipline::select('disciplines.id as id')
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->where('student_id', Auth::user()->id)
            ->where('disciplines.syllabus', $idkey)
            ->where('payed', '1')
            ->first();

        if (!empty($discipline->id)) {

            //returning file
            $fileLocation = $_SERVER["DOCUMENT_ROOT"] . '/syllabuses/' . $idkey . '.zip';
            if (file_exists($fileLocation)) {
                header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
                header("Cache-Control: public"); // needed for internet explorer
                header("Content-Type: application/zip");
                header("Content-Transfer-Encoding: Binary");
                header("Content-Length:" . filesize($fileLocation));
                header("Content-Disposition: attachment; filename=$idkey.zip");
                readfile($fileLocation);
                die();
            } else {
                die("Error: File not found.");
            }

        }
        return;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function payRegistrationFee(Request $request)
    {
        $profile = Profiles::select('paid', 'id')->where('user_id', Auth::user()->id)->first();

        if ($profile->paid == 1) return redirect()->route('financesPanel');

        $feePrice = config('app.registrationFee');
        $back = $request->input('back', '');

        return view('student.registrationFee', compact('feePrice', 'back'));
    }

    public function readonly()
    {
        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        $profile->readonly = 1;
        $profile->save();

        return redirect()->route('studentShopIndex');

    }

    public function updateBalance(Request $request)
    {
        $user = \App\Services\Auth::user();
        $balance = $user->refreshBalance();

        if ($balance === false) {
            abort(500);
        }

        return Response::json([
            'balance' => $balance
        ]);
    }

    public function submodulePay(Request $request, int $id) {
        if (empty($request->input('language_level'))) {
            abort(500);
        }

        $disciplineId = DisciplineSubmodule::getDisciplineIdByLanguageLevel($id, $request->input('language_level'));

        if (empty($disciplineId)) {
            abort(500, 'There is not such discipline');
        }

        $submoduleId = $request->input('submodule_id');

        // Buying
        if ($request->exists('buy')) {
            return redirect()->route('disciplinePay', ['id' => $disciplineId, 'submodule_id' => $submoduleId]);
        }
        // Partial Buy
        elseif ($request->exists('buyPartial')) {
            return redirect()->route('disciplinePartialPay', ['id' => $disciplineId, 'submodule_id' => $submoduleId]);
        }
        else {
            abort(500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxAddTransactionHistory( Request $request )
    {

        // validation data
        $obValidator = StudentAjaxGetTransactionHistoryValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error input data')
            ]);
        }

        $mTransactionService = TransactionService::addHistory(
            $request->input('iin'),
            $request->input('date_from'),
            $request->input('date_to')
        );

        if( !empty($mTransactionService) )
        {
            return \Response::json([
                'status'   => true,
                'message'  => __('Success')
            ]);
        }

        return \Response::json([
            'status'   => false,
            'message'  => __('Request error')
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetTransactionHistory( Request $request )
    {

        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (isset($column['search']['value']) && $column['search']['value'] != '') {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }

        $iin = $request->input('iin', false);
        if(!$iin)
        {
            $iin = \App\Services\Auth::user()->studentProfile->iin ?? '';
        }

        $searchData = TransactionHistory::getTransactionList(
            $iin,
            $request->input('search')['value'],
            $searchParams,
            $request->input('start'),
            $request->input('length'),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw'            => intval( $request->input('draw') ),
            'recordsTotal'    => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data'            => $searchData['data']
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirmMobile(Request $request)
    {
        $user = \App\Services\Auth::user();

        if($user->studentProfile && !$user->studentProfile->mobile_confirm)
        {
            return view('student.confirm_mobile', compact($user));
        }

        abort(404);
    }

    /**
     * Buying remote access
     * @param int $disciplineId
     * @param null $backTo Back to page
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function remoteAccessPay(int $disciplineId, $backTo = null, Request $request)
    {
        $studentDiscipline = StudentDiscipline::getOne(Auth::user()->id, $disciplineId);

        if (empty($studentDiscipline)) {
            $this->flash_danger('There is not such discipline');
            return redirect()->route('study');
        }

        if ($studentDiscipline->discipline->is_practice) {
            $this->flash_danger('You cannot buy remote access for practice');
            return redirect()->route('study');
        }

        //todo disable qr
        /*if (!Auth::user()->studentProfile->remote_exam_qr && Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME) {
            return redirect()->route('studentRemoteExamQR');
        }*/

        if (!empty($backTo)) {
            if ($backTo == 'test1') {
                $redirect = ['route' => 'studentQuiz', 'params' => [$disciplineId]];
            } elseif ($backTo == 'exam') {
                $redirect = ['route' => 'studentExam', 'params' => [$disciplineId]];
            } else {
                $redirect = ['route' => 'study', 'params' => []];
            }
        } else {
            $redirect = ['route' => 'study', 'params' => []];
        }

        // Already remote
        if ($studentDiscipline->remote_access || Auth::user()->free_remote_access) {
            return redirect()->route($redirect['route'], $redirect['params']);
        }

        $priceFor1Credit = Auth::user()->remote_access_price;

        // Price is empty
        if (empty($priceFor1Credit)) {
            $this->flash_danger('An error has occurred. Please contact the Registration Office by phone +77750073000.');
            return redirect()->route('study');
        }

        $request->session()->put('remote_access_redirect', $redirect);

        $service = FinanceNomenclature::getRemoteAccess($studentDiscipline->discipline->ects, $priceFor1Credit);

        $lowBalance = Auth::user()->balanceByDebt() < $service->cost;

        return view('student.remoteAccessPay', compact('studentDiscipline', 'lowBalance', 'priceFor1Credit', 'service'));
    }
}
	
	
