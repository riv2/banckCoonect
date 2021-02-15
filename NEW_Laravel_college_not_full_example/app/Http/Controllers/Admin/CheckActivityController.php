<?php

namespace App\Http\Controllers\Admin;

use App;
use Carbon\Carbon;
use App\ActivityLog;
use App\Profiles;
use App\Services\SearchCache;
use App\StudyGroup;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CheckActivityController extends Controller
{
    public function studentsList()
    {
        $groups = StudyGroup::select('id', 'name')->get();

        return view('admin.pages.activities.studentsList', compact('groups'));
    }

    public function teachersList()
    {
        return view('admin.pages.activities.teachersList');
    }

    public function getUsersListAjax(Request $request, $role_type)
    {

        $date['year'] = $request->input('year');
        $date['month'] = $request->input('month');
        $group = $request->input('group');
        $name = $request->input('name', '');
        $currentPage = $request->input('currentPage', 1);
        $pageCount = $request->input('pageLength', 10);

        if ($role_type === 'student'){

            $profilesIds = SearchCache::search(User::$adminRedisMatriculantTable, $name, 'fio');

            $profiles = Profiles::whereIn('user_id', $profilesIds);
            if (isset($group)){
                $profiles = $profiles->where('study_group_id', $group);
            }
            $profileTotalCount =  SearchCache::totalCount(User::$adminRedisMatriculantTable);
        } elseif ($role_type === 'teacher') {

            $profilesIds = SearchCache::search(ActivityLog::TEACHER_ONLINE_ACTIVITY, $name);
            $profileTotalCount = SearchCache::totalCount(ActivityLog::TEACHER_ONLINE_ACTIVITY);

            $profiles = User::whereIn('id', $profilesIds);
        }

        $profiles->offset(($currentPage - 1) * $pageCount)->limit($pageCount);
        $profiles = $profiles->get();

        if ($profiles) {
            $data = [];

            foreach ($profiles as $profile) {
                $logins_day = [];

                if($role_type === 'student'){
                    $user_id = $profile->user_id;
                } else {
                    $user_id = $profile->id;
                }

                $daysInMonth = Carbon::now()->daysInMonth;

                for ($day = 1; $day <= $daysInMonth; $day++){

                    $key = ActivityLog::getKeyInCache($date['year'], $date['month'], $user_id, $day);
                    $activityLog = SearchCache::getJsonData($key);

                    if (!empty($activityLog)){
                        $loginDays = json_decode($activityLog);
                        $logins_day[$day]["day_in_month"] = $day;

                        foreach ($loginDays as $page){
                            $logins_day[$day]['pages'][] = $page;
                        }
                    }
                }

                $data[] = [
                    "user_id" => $profile->user_id ?? $profile->id,
                    "user_full_name" => $profile->fio ?? $profile->name,
                    "logins_day" => $logins_day,
                ];
            }
            return response()->json([
                'profiles' => $data,
                'totalCount' => $profileTotalCount
            ]);
        }

        return false;
    }

    public function export(Request $request, $type)
    {
        ini_set("memory_limit", "1000M");
        ini_set('max_execution_time', 0);

        $date['year'] = $request->input('year', Carbon::now()->year);
        $date['month'] = $request->input('month', Carbon::now()->month);
        $group = $request->input('group');
        $name = $request->input('name');
        $offset = $request->input('offset', 0);
        $count = $request->input('count', 100);
        $data = [];

        $filters_info = [
            "year" =>  $date['year'],
            "month" => $date['month'],
            "group_name" => null,
        ];

        if ($type === 'student'){
            $profiles = Profiles::where('fio', 'LIKE', "%" . $name . "%")
                ->where('education_status', Profiles::EDUCATION_STATUS_STUDENT)
                ->where('check_level', 'or_cabinet')
                ->whereHas('user');

            if(!empty($group))
            {
                $profiles->where('study_group_id', '=', $group);
                $groupName = StudyGroup::select('id', 'name')->where('id', '=', $group)->first();

                $filters_info['group_name'] = $groupName->name ?? null;
            }
        } elseif ($type === 'teacher') {
            $profilesIds = SearchCache::search(ActivityLog::TEACHER_ONLINE_ACTIVITY, $name);
            $profiles = User::whereIn('id', $profilesIds);
        }

        $profiles->offset($offset)->limit($count - $offset);

        $profiles = $profiles->get();

        if (!empty($profiles)) {
            foreach ($profiles as $profile) {
                $activities = [];

                if($type === 'student'){
                    $user_id = $profile->user_id;
                } else {
                    $user_id = $profile->id;
                }

                if ($profile->activity_logs->count() > 0 ){
                    $activity_logs = ActivityLog::where('user_id', $user_id)
                        ->where('is_fake', 0)
                        ->whereIn('log_type', [$type === 'student' ? ActivityLog::STUDENT_ONLINE_ACTIVITY : ActivityLog::TEACHER_ONLINE_ACTIVITY])
                        ->whereYear('created_at', '=', $date['year'])
                        ->whereMonth('created_at', '=', $date['month'])
                        ->get();

                    foreach ($activity_logs as $activity_log) {
                        foreach ($activity_log['properties']['visited_pages'] as $page){
                            $activities[] = $page;
                        }
                    }
                }

                $data[] = [
                    "user_id" => $user_id,
                    "user_full_name" => $profile->fio ?? $profile->name,
                    "activities" => $activities,
                ];
            }
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView('admin.pages.activities.PDF', [
            'profiles' => $data,
            'filters_info' => $filters_info
        ]);

        return $pdf->download('activities.pdf');
    }
}
