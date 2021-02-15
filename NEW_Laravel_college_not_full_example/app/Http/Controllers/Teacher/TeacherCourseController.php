<?php

namespace App\Http\Controllers\Teacher;

use App\Course;
use App\Discipline;
use App\Services\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeacherCourseController extends Controller
{
    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function coursesEdit(Request $request, $id)
    {
        $course = Course::
            where('user_id', Auth::user()->id)->
            where('id', $id)->first();

        if(!$course && $id == 'add')
        {
            $course = new Course();
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }

        $hasDocument = Auth::user()->hasEducationDocument();
        $disciplineList = Discipline::get();
        $courseList = Course::where('user_id', Auth::user()->id)->get();

        return view('teacher.courses.edit', [
            'course'            => $course,
            'disciplineList'    => $disciplineList,
            'hasDocument'       => $hasDocument,
            'courseList'        => $courseList,
            'courseTab'         => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function coursesEditPost(Request $request, $id)
    {
        $ruleList = [
            'image'         => 'image',
            'description'   => 'required',
            'language'      => 'required',
            'certificate'   => 'file',
            'tags'          => 'required'
        ];

        $hasDocument = Auth::user()->hasEducationDocument();
        if($hasDocument)
        {
            $ruleList['discipline_id'] = 'required|exists:disciplines,id';
        }
        else
        {
            $ruleList['title'] = 'required';
        }

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $course = Course::
            where('user_id', Auth::user()->id)->
            where('id', $id)->first();

        if(!$course && $id == 'add')
        {
            $course = new Course();
            $course->user_id = Auth::user()->id;
        }
        elseif( $id != 'add' && !is_numeric($id) )
        {
            return view('errors.404');
        }

        $course->fill($request->all());
        $course->status = Course::STATUS_MODERATION;

        if($course->save())
        {
            $course->saveFiles($request->file('photo'), $request->file('certificate_file'));

            if($hasDocument) {
                $course->setDiscipline($request->input('discipline_id'));
            }
        }

        return redirect()->route('teacherCourseEdit', ['id' => $course->id]);
    }
}
