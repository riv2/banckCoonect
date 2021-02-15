<?php

namespace App\Http\Controllers\Admin;

use App\{Course,CourseTopics,Discipline,Profiles,User};
use App\Http\Controllers\Controller;
use App\Services\{Avatar};
use App\Teacher\{ProfileTeacher};
use App\Validators\{
    AdminCourseEditPostValidator,
    AdminCourseEditTopicValidator,
    AdminCourseEditTopicPostValidator,
    AdminCourseTopicDeleteValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log};

class CourseController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList(Request $request)
    {

        $courseList = Course::getListForAdmin();

        return view('admin.pages.courses.list', [
            'courseList' => $courseList,
        ]);
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {

        $oProfileTeacher = ProfileTeacher::
        where('status',ProfileTeacher::STATUS_ACTIVE)->
        get();

        if($id == 0)
        {

            $course = new Course();
        } else {

            $course = Course::
            with(['user' => function($query){
                $query->with('educationDocumentList');
                $query->with('teacherProfile');
            }])->
            where('id', $id)->first();

        }

        // init lang
        $aLanguage[ \App\Profiles::EDUCATION_LANG_RU ] =
        [
            'name' => \App\Profiles::EDUCATION_LANG_RU,
            'data' => ''
        ];
        $aLanguage[ \App\Profiles::EDUCATION_LANG_KZ ] =
        [
            'name' => \App\Profiles::EDUCATION_LANG_KZ,
            'data' => ''
        ];
        $aLanguage[ \App\Profiles::EDUCATION_LANG_EN ] =
        [
            'name' => \App\Profiles::EDUCATION_LANG_EN,
            'data' => ''
        ];
        if( !empty($course->language) )
        {
            $language = explode(',',$course->language);
            foreach( $language as $item )
            {
                if( $item == \App\Profiles::EDUCATION_LANG_RU )
                {
                    $aLanguage[ \App\Profiles::EDUCATION_LANG_RU ]['data'] = 1;
                } elseif( $item == \App\Profiles::EDUCATION_LANG_KZ )
                {
                    $aLanguage[ \App\Profiles::EDUCATION_LANG_KZ ]['data'] = 1;
                } elseif( $item == \App\Profiles::EDUCATION_LANG_EN )
                {
                    $aLanguage[ \App\Profiles::EDUCATION_LANG_EN ]['data'] = 1;
                }
            }
        }

        return view('admin.pages.courses.edit', [
            'course'   => $course,
            'teachers' => $oProfileTeacher,
            'language' => $aLanguage
        ]);

    }


    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editPost(Request $request, $id)
    {

        // validation data
        $obValidator = AdminCourseEditPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        if($id == 0)
        {

            $course = new Course();
        } else {

            $course = Course::
            with(['user' => function($query){
                $query->with('educationDocumentList');
            }])->
            where('id', $id)->
            first();
        }

        if(!$course)
        {
            abort(404);
        }


        if( !empty($request->input('language')) )
        {
            $language = implode(',',$request->input('language'));
        }


        $course->fill($request->all());
        $course->language = $language;
        $course->status = $request->input('status') == 'true' ? Course::STATUS_ACTIVE : Course::STATUS_MODERATION;

        if($course->save())
        {
            $course->saveFiles(
                $request->file('photo'),
                $request->file('author_resume_file'),
                $request->file('certificate_file_name'),
                $request->file('scheme_courses_file'),
                $request->file('trial_course_file'),
                $request->file('inner_photo')
            );
        }

        $request->session()->flash('flash_message', 'Изменения сохранены.');
        return redirect()->route('adminCourseEdit', ['id' => $course->id]);

    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(Request $request, $id)
    {
        $course = Course::where('id', $id)->first();

        if(!$course)
        {
            abort(404);
        }

        $oCourseTopics = CourseTopics::
        where('courses_id',$id)->
        whereNull('deleted_at')->
        delete();

        $course->delete();

        return redirect()->route('adminCourseList');
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getTopicsList( Request $request, $id)
    {

        $course       = Course::where('id',$id)->first();
        $courseListRu = CourseTopics::getListForAdmin($id, Profiles::EDUCATION_LANG_RU);
        $courseListKz = CourseTopics::getListForAdmin($id, Profiles::EDUCATION_LANG_KZ);
        $courseListEn = CourseTopics::getListForAdmin($id, Profiles::EDUCATION_LANG_EN);

        if( empty($course) )
        {
            abort(404);
        }

        return view('admin.pages.courses.listTopic', [
            'course'       => $course,
            'courseTopicListRu' => $courseListRu,
            'courseTopicListKz' => $courseListKz,
            'courseTopicListEn' => $courseListEn
        ]);

    }


    /**
     * @param Request $request
     * @param $id
     * @return $this
     */
    public function editTopic(Request $request, $id)
    {

        // validation data
        $obValidator = AdminCourseEditTopicValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        $oCourse = Course::where('id',$request->input('course_id'))->first();

        if( $id == 0 )
        {

            $oCourseTopics = new CourseTopics();
        } else {

            $oCourseTopics = CourseTopics::
            where('id',$id)->
            whereNull('deleted_at')->
            first();
        }

        if( empty($oCourse) || empty($oCourseTopics) )
        {
            abort(404);
        }

        return view('admin.pages.courses.editTopic', [
            'course'  => $oCourse,
            'topic'   => $oCourseTopics
        ]);

    }


    /**
     * @param Request $request
     * @param $id
     */
    public function editTopicPost(Request $request, $id)
    {

        // validation data
        $obValidator = AdminCourseEditTopicPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        $oCourse = Course::where('id',$request->input('course_id'))->first();

        if( $id == 0 )
        {

            $oCourseTopics = new CourseTopics();
        } else {

            $oCourseTopics = CourseTopics::
            where('id',$id)->
            whereNull('deleted_at')->
            first();
        }

        if( empty($oCourse) || empty($oCourseTopics) )
        {
            abort(404);
        }

        $oCourseTopics->fill( $request->all() );
        $oCourseTopics->courses_id = $oCourse->id;

        if($oCourseTopics->save())
        {
            $oCourseTopics->saveFiles(

                $request->file('resource_file')
            );
        }

        $request->session()->flash('flash_message', 'Изменения сохранены.');
        return redirect()->route('adminCourseEditTopic', ['id' => $oCourseTopics->id, 'course_id' => $oCourse->id ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeTopic(Request $request, $id)
    {

        $oCourseTopic = CourseTopics::where('id', $id)->first();
        if( empty($oCourseTopic) )
        {
            abort(404);
        }
        $iCourseId = $oCourseTopic->courses_id;
        $oCourseTopic->delete();

        return redirect()->route('adminCourseTopicsList',['id'=>$iCourseId]);
    }


}
