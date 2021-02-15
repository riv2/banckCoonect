<?php

namespace App\Http\Controllers\Admin;

use App\ActivityLog;
use App\Profiles;
use App\StudyGroup;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StudentVisitsController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $groups = StudyGroup::select('id', 'name')->get();

        return view('admin.pages.visits.index', compact('groups'));
    }

    /**
     * @param Request $request
     * @return bool|\Illuminate\Http\JsonResponse
     *
     * list with user profiles which contain visits info related to query params
     */
    public function getListAjax(Request $request)
    {

        $date['year'] = $request->input('year');
        $date['month'] = $request->input('month');
        $group = $request->input('group');
        $name = $request->input('name');
        $currentPage = $request->input('currentPage', 1);
        $pageCount = $request->input('pageLength', 10);

        $profiles = Profiles::with(['user' => function ($q) {
                return $q->whereNull('deleted_at');
            }])
            ->with(['studentCheckins' => function($q) use($date) {
                $q->whereYear('created_at', '=', $date['year'])->whereMonth('created_at', '=', $date['month']);
                $q->with(['teacher' => function($q1){
                    $q1->select(['id', 'name']);
                }]);
            }])
            ->with(['studentsDisciplines' => function($q) use($date) {
                $q->select(['id', 'student_id', 'test1_date', 'test1_qr_checked', 'test_date', 'test_qr_checked']);
                $q->where(function($q1) use($date) {
                    $q1->where('test1_qr_checked', true);
                    $q1->whereYear('test1_date', '=', $date['year'])->whereMonth('test1_date', '=', $date['month']);
                });

                $q->orWhere(function($q1) use($date) {
                    $q1->where('test_qr_checked', true);
                    $q1->whereYear('test_date', '=', $date['year'])->whereMonth('test_date', '=', $date['month']);
                });
            }])
            ->whereHas('user')
            ->where('fio', 'LIKE', "%" . $name . "%")
            ->where('education_status', Profiles::EDUCATION_STATUS_STUDENT)
            ->whereIn('education_study_form', [
                Profiles::EDUCATION_STUDY_FORM_FULLTIME,
                Profiles::EDUCATION_STUDY_FORM_EVENING
            ]);

        if($group)
        {
            $profiles->where('study_group_id', '=', $group);
        }

        $profileTotalCount = $profiles;
        $profileTotalCount = $profileTotalCount->count();
        $profiles->offset(($currentPage - 1) * $pageCount)->limit($pageCount);
        $profiles = $profiles->get();

        if ($profiles) {
            $data = [];

            foreach ($profiles as $profile) {

                $lecture_list = [];
                $other_discipline_list = [];
                $online_list = [];

                if ($profile->studentCheckins) {

                    $count = 0;
                    foreach ($profile->studentCheckins as $checkin) {

                        $lecture_list[$count]["discipline_name"] = "Занятие";
                        $lecture_list[$count]["visits_time"] = date(
                            'd.m.Y H:i',
                            strtotime($checkin->created_at) + (6*3600)
                        );
                        $lecture_list[$count]["day_in_month"] = $checkin->created_at->format('d');
                        $lecture_list[$count]["teacher_fio"] = $checkin->teacher->name ?? '';

                        $count++;
                    }

                }

                if ($profile->studentsDisciplines) {

                    $otherDiscCount = 0;
                    foreach ($profile->studentsDisciplines as $discipline) {
                        if ($discipline->test1_qr_checked) {
                            $other_discipline_list[$otherDiscCount]["discipline_name"] = "Тест 1";
                            $other_discipline_list[$otherDiscCount]["visits_time"] = date(
                                'd.m.Y H:i',
                                strtotime($discipline->test1_date) + (6 * 3600)
                            );
                            $other_discipline_list[$otherDiscCount]["day_in_month"] = $discipline->test1_date->format('d');

                            $otherDiscCount++;

                        }

                        if ($discipline->test_qr_checked) {

                            $other_discipline_list[$otherDiscCount]["discipline_name"] = "Экзамен";
                            $other_discipline_list[$otherDiscCount]["visits_time"] = date(
                                'd.m.Y H:i',
                                strtotime($discipline->test_date) + (6 * 3600)
                            );
                            $other_discipline_list[$otherDiscCount]["day_in_month"] = $discipline->test_date->format('d');

                            $otherDiscCount++;
                        }

                    }
                }

                if ($profile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE) {
                    $activityUserLog = ActivityLog::where('user_id', $profile->user_id)
                                                    ->where('log_type', ActivityLog::AUTH_ONLINE_LOG)
                                                    ->whereMonth('created_at', '=', $date['month'])
                                                    ->whereYear('created_at', '=', $date['year'])
                                                    ->get();

                    foreach ($activityUserLog as $log) {
                        $online_list[] = [
                            'visits_time' => $log->properties['from'],
                            'day_in_month' => Carbon::parse($log->properties['from'])->format('d'),
                        ];
                    }
                }

                $data[] = [
                    "user_id" => $profile->user_id,
                    "user_full_name" => $profile->fio,
                    "lecture_list" => $lecture_list,
                    "online_list" => $online_list,
                    "other_discipline_list" => $other_discipline_list
                ];

            }

            return response()->json([
                'profiles' => $data,
                'totalCount' => $profileTotalCount
            ]);
        }

        //TODO: error message
        return false;
    }

}
