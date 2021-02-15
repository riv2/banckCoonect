<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\StudyGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\App;

class StudentVisitsExportController extends Controller
{

    /**
     * @return mixed
     *
     * download a pdf file for print
     */
    public function printVisitsPDF(Request $request)
    {

        $date['year'] = $request->input('year');
        $date['month'] = $request->input('month');
        $group = $request->input('group');
        $name = $request->input('name');
        $partNum = $request->input('partNum');

        $groupName = null;

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
                $q->with('discipline');
                $q->select(['id', 'student_id', 'test1_date', 'test1_qr_checked', 'test_date', 'test_qr_checked', 'discipline_id']);
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
            ->where('fio', 'LIKE', "%" . $name . "%");

        if($group)
        {
            $profiles->where('study_group_id', '=', $group);
            $groupName = StudyGroup::select('id', 'name')->where('id', '=', $group)->first();
        }

        $profiles = $profiles
            ->offset(($partNum - 1) * 100)
            ->limit(100)
            ->get();

        if ($profiles) {
            $data = [];
            $lecture_list = [];
            $other_discipline_list = [];
            foreach ($profiles as $profile) {

                $lecture_list = [];
                $other_discipline_list = [];

                if($profile->studentCheckins) {

                    $count = 0;
                    foreach ($profile->studentCheckins as $checkin) {

                        $lecture_list[$count]["discipline_name"] = "Занятие";
                        $lecture_list[$count]["visits_time"] = date(
                            'd.m.Y H:i',
                            strtotime($checkin->created_at) + (6*3600)
                        );
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
                            $other_discipline_list[$otherDiscCount]["subject_name"] = $discipline->discipline->name ?? '';

                            $otherDiscCount++;

                        }

                        if ($discipline->test_qr_checked) {

                            $other_discipline_list[$otherDiscCount]["discipline_name"] = "Экзамен";
                            $other_discipline_list[$otherDiscCount]["visits_time"] = date(
                                'd.m.Y H:i',
                                strtotime($discipline->test_date) + (6 * 3600)
                            );
                            $other_discipline_list[$otherDiscCount]["subject_name"] = $discipline->name ?? '';

                            $otherDiscCount++;
                        }

                    }
                }

                $data[] = [
                    "user_id" => $profile->user_id,
                    "user_full_name" => $profile->fio,
                    "lecture_list" => $lecture_list,
                    "other_discipline_list" => $other_discipline_list,
                ];


                $filters_info = [
                    "year" =>  $date['year'],
                    "month" => $date['month'],
                    "group_name" => $groupName->name ?? null
                ];

            }

            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('admin.pages.visits.PDF', ['profiles' => $data, 'filters_info' => $filters_info]);

            return $pdf->download('visits.pdf');

        }

        // TODO: error handling
        return false;

    }
}
