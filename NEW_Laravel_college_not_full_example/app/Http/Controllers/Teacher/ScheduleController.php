<?php

namespace App\Http\Controllers\Teacher;

use App\Services\MirasApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\Services\Auth;
use App\Lecture;
use Illuminate\Support\Carbon;

class ScheduleController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request, $id)
    {
        $course = Course::
        where('user_id', Auth::user()->id)->
        where('id', $id)->
        first();

        if(!$course)
        {
            abort(404);
        }

        $courseList = Course::where('user_id', Auth::user()->id)->get();
        $lectureList = Lecture::where('course_id', $course->id)->
            orderBy('start')->
            get();

        setlocale(LC_TIME, 'ru_RU.UTF-8');
        $dateList = [];
        foreach ($lectureList as $lecture)
        {
            $lecture->room = MirasApi::request(MirasApi::ROOM_RESERVE_INFO, [
                'id' => $lecture->room_booking_id
            ]);

            $date = new Carbon();
            $date = $date->parse($lecture->start)->formatLocalized('%d %B %Y (%A)');
            $dateList[$date][] = $lecture;
        }


        return view('teacher.courses.edit', [
            'course'            => $course,
            'courseList'        => $courseList,
            'dateList'          => $dateList,
            'scheduleTab'       => true
        ]);
    }
}
