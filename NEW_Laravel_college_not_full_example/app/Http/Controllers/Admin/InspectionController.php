<?php

namespace App\Http\Controllers\Admin;

use App\BcApplicationConfig;
use App\BcApplications;
use App\MgApplicationConfig;
use App\MgApplications;
use App\NotificationTemplate;
use App\Profiles;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\User;
use App\Services\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InspectionController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request, $tab)
    {
        $bcConfig = BcApplicationConfig::first();
        $mgConfig = MgApplicationConfig::first();

        if(!$bcConfig)
        {
            abort(500);
        }

        return view('admin.pages.inspection.list', [
            'bcConfig'      => $bcConfig,
            'mgConfig'      => $mgConfig,
            'tab'           => $tab
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editBcApplicationPost(Request $request)
    {
        $bcConfig = BcApplicationConfig::first();

        if(!$bcConfig)
        {
            abort(500);
        }

        $bcConfig->fill($request->all());
        $bcConfig->save();

        return redirect()->route('adminInspectionList', ['tab' => 'bachelor']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editMgApplicationPost(Request $request)
    {
        $mgConfig = MgApplicationConfig::first();

        if(!$mgConfig)
        {
            abort(500);
        }

        $mgConfig->fill($request->all());
        $mgConfig->save();

        return redirect()->route('adminInspectionList', ['tab' => 'master']);
    }

    public function getMatriculantsList()
    {
        $orderList = Order::get();
        $notificationTemplates = NotificationTemplate::getListForAdmin();

        $sAuthUser = Auth::user()->name;

        $langList = Profiles::$list;
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

        $base_education = $base_education->unique();
        $study_forms = Profiles::$studyForms;
        $categories = Profiles::$categories;
        $degree = Profiles::$degree;
        $status = $status->unique();

        return view('admin.pages.inspection.matriculants.list', compact(
            'orderList',
            'notificationTemplates',
            'sAuthUser',
            'years',
            'study_forms',
            'categories',
            'degree',
            'langList',
            'status',
            'base_education'
        ));
    }

    /**
     * @param $category
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMatriculantsListAjax($category, Request $request)
    {
        $searchParams = [];

        if ($request->has('columns')) {
            foreach ($request->input('columns') as $i => $column) {
                if (!empty($column['search']['value'])) {
                    $searchParams[$i] = $column['search']['value'];
                }
            }
        }

        if ($category != 'all') {
            $searchParams[10] = $category;
        }
        $searchParams[13] = $request->get('deleted') ?? 0;

        $searchData = \App\User::getMatriculantListForAdmin(
            !$searchParams[13] ? Profiles::CHECK_LEVEL_INSPECTION : '',
            $request->input('search')['value'],
            $searchParams,
            $request->input('start'),
            $request->input('length', 10),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return \Response::json([
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
        $user = \App\User::where('id', $request->input('user_id'))->first();
        $profile = $user->studentProfile;

        if(!$profile)
        {
            abort(404);
        }

        $profile->check_level = $request->input('new_level');
        $profile->save();

        return \Response::json();
    }

    public function moveToOR(Request $request)
    {
        $userIds = $request->input('users');

        if (empty($userIds)) {
            return \Response::json(['status' => false, 'error' => 'Необходимо выбрать студентов']);
        }

        User::usersMoveToOR($userIds);

        return \Response::json(['status' => true]);
    }
}
