<?php

namespace App\Http\Controllers\Teacher;

use App\Lecture;
use App\Services\MirasApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Course;
use App\Services\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class LectureController extends Controller
{
    public function getList(Request $request, $courseId)
    {
        $course = Course::
        where('user_id', Auth::user()->id)->
        where('id', $courseId)->
        first();

        if(!$course)
        {
            abort(404);
        }

        $courseList = Course::where('user_id', Auth::user()->id)->get();
        $lectureList = Lecture::where('course_id', $course->id)->get();

        return view('teacher.courses.edit', [
            'course'            => $course,
            'courseList'        => $courseList,
            'lectureList'       => $lectureList,
            'lecturesTab'       => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $courseId, $lectureId)
    {
        $course = Course::
            where('user_id', Auth::user()->id)->
            where('id', $courseId)->
            first();

        if(!$course)
        {
            abort(404);
        }

        $lecture = Lecture::
            where('course_id', $courseId)->
            where('id', $lectureId)->
            first();

        if(!$lecture && $lectureId == 'add')
        {
            if(!$course->canCreateLecture())
            {
                abort(404);
            }

            $lecture = new Lecture();
            $lecture->course_id = $course->id;
        }
        elseif( $lectureId != 'add' && !is_numeric($lectureId) )
        {
            abort(404);
        }

        $lecture->getReserveRoomInfo();

        $hasDocument = Auth::user()->hasEducationDocument();
        $courseList = Course::where('user_id', Auth::user()->id)->get();
        $lectureList = Lecture::where('course_id', $course->id)->get();
        $buildingList = MirasApi::request(MirasApi::BUILDING_LIST);
        $stuffList = MirasApi::request(MirasApi::STUFF_LIST);

        return view('teacher.courses.edit', [
            'course'            => $course,
            'lecture'           => $lecture,
            'lectureList'       => $lectureList,
            'hasDocument'       => $hasDocument,
            'courseList'        => $courseList,
            'buildingList'      => $buildingList,
            'stuffList'         => $stuffList,
            'lecturesTab'       => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPost(Request $request, $courseId, $lectureId)
    {
        $course = Course::
            where('user_id', Auth::user()->id)->
            where('id', $courseId)->
            first();

        if(!$course)
        {
            abort(404);
        }

        if(!$course->canCreateLecture())
        {
            abort(404);
        }

        /*Validation start*/
        $ruleList = [
            'title'         => 'required|max:250',
            'duration'      => 'required|integer|min:0|max:5',
            'start'         => 'required',
            'type'          => ['required', Rule::in([Lecture::TYPE_ONLINE, Lecture::TYPE_OFFLINE, Lecture::TYPE_ALL])],
            'cost'          => 'required|numeric',
            'multimedia'    => 'nullable|boolean'
        ];

        if($request->input('type') == Lecture::TYPE_ONLINE || $request->input('type') == Lecture::TYPE_ALL)
        {
            $ruleList['url'] = 'required|url';
        }

        if($request->input('type') == Lecture::TYPE_OFFLINE || $request->input('type') == Lecture::TYPE_ALL)
        {
            $ruleList['room.building_id']           = 'nullable|integer';
            $ruleList['room.seats_count']           = 'required|integer';
            $ruleList['room.type']                  = 'required';
            $ruleList['room.conditioner']           = ['nullable', Rule::in(['no_matter', 'yes', 'no'])];
            $ruleList['room.stuff']                 = 'nullable|array';
        }

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }
        /*Validation end*/

        $lecture = Lecture::
        where('course_id', $courseId)->
        where('id', $lectureId)->
        first();

        if(!$lecture && $lectureId == 'add')
        {
            if(!$course->canCreateLecture())
            {
                abort(404);
            }

            $lecture = new Lecture();
            $lecture->course_id = $course->id;
        }
        elseif( $lectureId != 'add' && !is_numeric($lectureId) )
        {
            abort(404);
        }

        $lecture->getReserveRoomInfo();

        $needChangeRoom = false;
        $needDeleteRoom = false;
        if(
            $lecture->type != $request->input('type') &&
            $request->input('type') == Lecture::TYPE_ONLINE &&
            isset($lecture->room->id)
        )
        {
            $needDeleteRoom = true;
        }
        if($lecture->id && isset($lecture->room->id))
        {
            if(
                $lecture->start != date('Y-m-d H:i:s', strtotime($request->input('start'))) ||
                $lecture->duration != $request->input('duration') ||
                $lecture->room->type != $request->input('room.type') ||
                $lecture->room->seats_count != $request->input('room.seats_count') ||
                $lecture->room->conditioner != $request->input('room.conditioner') ||
                $lecture->room->building_id != $request->input('room.building_id')
            )
            {
                $needChangeRoom = true;
            }
        }

        if($needDeleteRoom || $needChangeRoom)
        {
            $lecture->deleteReserveRoom();
        }

        $lecture->fill($request->all());
        unset($lecture->room);
        $lecture->save();

        if(
            (($lecture->type == Lecture::TYPE_OFFLINE || $lecture->type == Lecture::TYPE_ALL) && !$lecture->room_booking_id)
        ) {
            $roomParams = $request->input('room');
            $lecture->reserveRoom($request->input('start'), $request->input('duration'), $roomParams);
        }
        return redirect()->route('teacherLectureEdit', [
            'courseId' => $course->id,
            'lectureId' => $lecture->id
        ]);
    }
}
