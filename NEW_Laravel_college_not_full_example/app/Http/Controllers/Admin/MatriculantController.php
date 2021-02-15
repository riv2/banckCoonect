<?php

namespace App\Http\Controllers\Admin;

use App\{AuditFinanceNomenclatures,
    BcApplications,
    FinanceNomenclature,
    Language,
    Order,
    Profiles,
    User,
    MgApplications};
use App\Http\Controllers\Controller;
use App\Services\{Auth, FinanceNomenclatureService, Service1C};
use App\Validators\{AdminMatriculantAjaxAttachServiceValidator, AdminMatriculantGetUserDataByIdsValidator};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Response};
use phpDocumentor\Reflection\Types\Self_;


class MatriculantController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $orderList = Order::get();
        $oFinanceNomenclature = FinanceNomenclature::
        where('or_hidden',FinanceNomenclature::HIDDEN_OR_NO)->
        whereNull('deleted_at')->
        whereNotNull('cost')->
        get();

        $sCurrentLocale = app()->getLocale();
        $locale = Language::getFieldName('name', $sCurrentLocale);

        $status = collect();
        $base_education = collect();
        $profiles = Profiles::select(DB::raw('distinct education_status'))->get();

        foreach ($profiles as $user){
            if($user->education_status !== null){
                $status->push($user->education_status);
            }
        }
        $bcApplications = BcApplications::select(DB::raw('distinct education'))->get();
        foreach ($bcApplications as $bcApplication){
            if ($bcApplication->education){
                $base_education->push(['key' => __($bcApplication->education . '_origin'), 'value' => __($bcApplication->education)]);
            }
        }
        $mgApplications = MgApplications::select(DB::raw('distinct education'))->get();
        foreach ($mgApplications as $mgApplication){
            if ($mgApplication->education){
                $base_education->push(['key' => __($mgApplication->education . '_origin'), 'value' => __($mgApplication->education)]);
            }
        }
        $years = User::select(DB::raw('distinct year(created_at) as year'))->get()->sort();

        $status = $status->unique();
        $base_education = $base_education->unique();
        $langList = Profiles::$list;
        $study_forms = Profiles::$studyForms;
        $categories = Profiles::$categories;
        $degree = Profiles::$degree;

        return view('admin.pages.matriculants.list', compact(
            'orderList',
            'oFinanceNomenclature',
            'locale',
            'years',
            'study_forms',
            'categories',
            'langList',
            'degree',
            'status',
            'base_education'
        ));
    }

    /**
     * @param $category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax($category, Request $request)
    {
        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (isset($column['search']['value']) && $column['search']['value'] != '') {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }
        if ($category != 'all') {
            if ($category == 'send_down') {
                $searchParams[5] = $category;
            } else {
                $searchParams[10] = $category;
            }
        }

        $searchParams[13] = $request->get('deleted') ?? 0;

        $searchData = User::getMatriculantListForAdmin(
            !$searchParams[13] ? Profiles::CHECK_LEVEL_OR_CABINET : '',
            $request->input('search')['value'],
            $searchParams,
            $request->input('start', 0),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeCheckLevel(Request $request)
    {
        $user = User::where('id', $request->input('user_id'))->first();
        $profile = $user->studentProfile;

        if (!$profile) {
            abort(404);
        }

        $profile->check_level = $request->input('new_level');
        $profile->save();

        return \Response::json();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetUsersDataByIds(Request $request)
    {

        // validation data
        $obValidator = AdminMatriculantGetUserDataByIdsValidator::make($request->all());
        if ($obValidator->fails()) {
            return \Response::json([
                'status' => false,
                'message' => __('Error, No users attached')
            ]);
        }

        $oProfiles = Profiles::
        select([
            'user_id as id',
            'fio',
            'iin',
        ])->
        whereIn('user_id', $request->input('ids'))->
        get();

        if (empty($oProfiles) || (count($oProfiles) < 1)) {
            return \Response::json([
                'status' => false,
                'message' => __('Error, No users attached')
            ]);
        }

        return \Response::json([
            'status' => true,
            'data' => $oProfiles
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxAttachService(Request $request)
    {

        // validation data
        $obValidator = AdminMatriculantAjaxAttachServiceValidator::make($request->all());
        if ($obValidator->fails()) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        // множитель стоимости услуги
        $iCount = intval($request->input('count'));

        $oFinanceNomenclature = FinanceNomenclature::
        where('id', $request->input('service'))->
        first();

        if (empty($oFinanceNomenclature)) {
            return \Response::json([
                'status' => false,
                'isNotApprove' => true,
                'message' => __('Error, no service selected')
            ]);
        }

        $oUser = User::
        with('studentProfile')->
        whereIn('id', $request->input('ids'))->
        get();

        if (empty($oUser)) {
            return \Response::json([
                'status' => false,
                'isNotApprove' => true,
                'message' => __('Error, users not found')
            ]);
        }

        $aIIN = [];
        $bIsNotApprove = false;
        foreach ($oUser as &$item) {

            if (!empty($item->studentProfile->iin) && (in_array($item->studentProfile->iin, $aIIN) === false)) {
                $aIIN[] = $item->studentProfile->iin;
            }

            $item->fio = $item->studentProfile->fio ?? '';
            $item->iin = $item->studentProfile->iin ?? '';
            $item->notApprove = false;

            // TODO убрали проверку баланса студика
            /*
            if (intval($item->balance) < intval($oFinanceNomenclature->cost * $iCount)) {

                $bIsNotApprove = true;
                $item->notApprove = true;
            }
            */
        }

        // if $bIsNotApprove true return error
        if ($bIsNotApprove) {
            return \Response::json([
                'status' => false,
                'isNotApprove' => true,
                'data' => $oUser,
                'message' => __('Error, there are students with insufficient balance')
            ]);
        }

        // если справки
        if ($oFinanceNomenclature->code == '00000003274') {

            foreach ($oUser as &$itemUser) {

                // вызываем обработчик покупки справок - в поп. услугах профайла юзера
                $response = FinanceNomenclatureService::buy($request->input('service'), $itemUser->id, $iCount);

                $oStatus = json_decode($response->content());

                $itemUser->fio = $itemUser->studentProfile->fio ?? '';
                $itemUser->iin = $itemUser->studentProfile->iin ?? '';
                $itemUser->notApprove = !$oStatus->status;

            }

            return \Response::json([
                'status' => true,
                'isNotApprove' => true,
                'data' => $oUser,
                'message' => __('Request sent successfully')
            ]);


        } else {

            // send request to 1C
            $bResponse = Service1C::pay(
                $aIIN,
                $oFinanceNomenclature->code,
                intval($oFinanceNomenclature->cost * $iCount)
            );

        }


        foreach ($oUser as $itemUser) {

            $oAFN = new AuditFinanceNomenclatures();
            $oAFN->fill([
                'user_id' => $itemUser->id,
                'user_name' => $itemUser->studentProfile->fio ?? $itemUser->name,
                'owner_id' => Auth::user()->id,
                'owner_name' => Auth::user()->name,
                'service_id' => $oFinanceNomenclature->id,
                'service_name' => $oFinanceNomenclature->name,
                'service_code' => $oFinanceNomenclature->code,
                'cost' => intval($oFinanceNomenclature->cost * $iCount),
                'count' => $iCount,
                'status' => $bResponse ? AuditFinanceNomenclatures::STATUS_SUCCESS : AuditFinanceNomenclatures::STATUS_FAIL
            ]);
            $oAFN->save();
            unset($oAFN);

        }

        return \Response::json([
            'status' => $bResponse,
            'message' => $bResponse ? __('Request sent successfully') : __('Error when sending the request')
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetAuditList(Request $request)
    {

        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (isset($column['search']['value']) && $column['search']['value'] != '') {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }

        $searchData = AuditFinanceNomenclatures::getAuditList(
            $request->input('search')['value'],
            $searchParams,
            $request->input('start'),
            $request->input('length'),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data' => $searchData['data']
        ]);

    }

    public function moveToInspection(Request $request)
    {
        $userIds = $request->input('users');

        if (empty($userIds)) {
            return \Response::json(['status' => false, 'error' => 'Необходимо выбрать студентов']);
        }

        User::usersMoveToInspection($userIds);

        return \Response::json(['status' => true]);
    }

    public function setBuying(Request $request)
    {
        $userId = $request->input('user_id');
        $buying = $request->input('buying');

        if (empty($userId)) {
            return \Response::json(['success' => false, 'error' => 'Ошибка. Не выбран студент']);
        }

        /** @var User $user */
        $user = User::where('id', $userId)->first();
        $user->studentProfile->buying_allow = $buying;
        $user->studentProfile->save();

        return \Response::json(['success' => true]);
    }
}
