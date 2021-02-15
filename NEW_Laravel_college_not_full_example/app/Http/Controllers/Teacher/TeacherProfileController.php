<?php

namespace App\Http\Controllers\Teacher;

use App\Course\Course;
use App\Http\Controllers\Controller;
use App\Services\Avatar;
use App\Services\SmartId;
use App\Teacher\ProfileTeacher;
use App\ProfileDoc;
use App\User;
use App\UserEducationDocument;
use Illuminate\Http\Request;
use App\Services\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

class TeacherProfileController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.profile');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teacher\ProfileTeacher  $profileTeacher
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileTeacher $profileTeacher)
    {
        $profileTeacher = Auth::user()->teacherProfile;

        return view('teacher.profile', [
            'profileTeacher' => $profileTeacher
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $teacher = Auth::user();
        $profile = ProfileTeacher::where('user_id', '=', Auth::user()->id)->first();

        return view('teacher.dashboard', compact('profile', 'teacher'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileID()
    {
        return view('teacher.profileID');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileIDPost(Request $request)
    {
        $profileTeacher = Auth::user()->teacherProfile;
        if(!$profileTeacher) {
            $profileTeacher = new ProfileTeacher();
            $profileTeacher->user_id = Auth::user()->id;
        }

        ProfileDoc::saveDocument('front_id_photo', $request->file('front'));
        ProfileDoc::saveDocument('back_id_photo', $request->file('back'));

        $profileTeacher->save();

        return redirect()->route('teacherProfileCreate');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileEdit()
    {
        $profileTeacher = Auth::user()->teacherProfile;
        $educationDocument = Auth::user()->educationDocumentFirst();

        $front = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->first();
        $back = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_BACK_ID)->first();

        /* Parse document photo */
        $SID = SmartId::parseAll([
            public_path($front->getPathForDoc(ProfileDoc::TYPE_FRONT_ID, $front->filename) . $front->filename . ProfileDoc::EXT ),
            public_path($back->getPathForDoc(ProfileDoc::TYPE_BACK_ID, $back->filename) . $back->filename . ProfileDoc::EXT )
        ]);

        $SID->fio = implode(' ', [
            $SID->surname     ?? null,
            $SID->name        ?? null,
            $SID->patronymic  ?? null
        ]);

        $profileTeacher->combineWithSmartId($SID);

        return view('teacher.profileEdit', compact('profileTeacher', 'educationDocument'));
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function profileEditPost(Request $request)
    {
        $ruleList = [
                'iin'       => 'required|numeric|min:12',
                'fio'       => 'required|min:2',
                'bdate'     => 'required|date',
                'doctype'   => ['required', Rule::in(['pass', 'id'])],
                'docnumber' => 'required|min:2',
                'issuing'   => 'required|min:2',
                'issuedate' => 'required|date',
                'sex'       => ['required', Rule::in(['male', 'female'])],
                'mobile'    => 'required|min:7'
            ];

        if(!Auth::user()->teacherProfile->photo)
        {
            $ruleList['photo'] = 'required|image';
        }

        if($request->input('education_document.level'))
        {
            $ruleList['education_document.level'] = [Rule::in([
                UserEducationDocument::LEVEL_HIGHER,
                UserEducationDocument::LEVEL_SECONDARY_SPECIAL,
                UserEducationDocument::LEVEL_SECONDARY
            ])];
            $ruleList['education_document.doc_number']          = 'required';
            $ruleList['education_document.doc_series']          = 'required';
            $ruleList['education_document.institution_name']    = 'required';
            $ruleList['education_document.date']                = 'required|date';

            if(!Auth::user()->educationDocumentFirst() || !Auth::user()->educationDocumentFirst()->supplement_file_name) {
                $ruleList['education_document.supplement_file'] = 'required|image';
            }
        }

        $validator = \Validator::make($request->all(), $ruleList);
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        Auth::user()->teacherProfile->fill($request->all());
        if(Auth::user()->teacherProfile->save() && $request->has('photo'))
        {
            Avatar::make($request->file('photo'))->save( Auth::user()->teacherProfile->photo);
        }

        $document = UserEducationDocument::where('user_id', Auth::user()->id)->first();

        if($document) {
            if ($request->has('education_document.supplement_file')) {
                $request->file('education_document.supplement_file')->move(public_path('images/uploads/diploma'), $document->supplement_file_name);
            }
            if ($request->has('education_document.nostrification_file')) {
                $request->file('education_document.nostrification_file')->move(public_path('images/uploads/diploma'), $document->nostrification_file_name);
            }
        }

        return redirect()->route('teacherProfile');
    }

    public function coursesAddForm()
    {
        return view('teacher.courses.add');
    }

    public function coursesAddCreate(Request $request, Course $course)
    {
        try {
            $course->user_id = \auth()->user()->id;
            $course->title = $request->title;
            $course->description = $request->description;
            $course->price = $request->price;
            $course->start_at = $request->start_at;
            $course->end_at = $request->end_at;
            $course->period_number = $request->period_number;
            $course->period_interval = $request->period_interval;
            $course->duration = $request->duration;
            $course->image = $request->image;

            $photoName = time().'.'.$request->image->getClientOriginalExtension();
            $request->image->move(public_path('images/uploads/courses'), $photoName);
            $course->image = $photoName;

            $course->save();
        } catch (Exception $exception){
//            redirect()->withErrors();
        }

        return redirect('/teacher/courses/list');
    }

    public function coursesList(Auth $auth)
    {

        $myCourses = Course::all()->where('user_id', $auth::user()->id);
//        dd($myCourses);


        return view('teacher.courses.list', [
            'myCourses' => $myCourses
        ]);
    }

    public function courseDelete($id, Auth $auth)
    {
        $myCourse = Course::where('user_id', $auth::user()->id)
            ->where('id', $id)
            ->delete();

        return redirect('/teacher/courses/list');
    }

    public function courseEditView($id, Auth $auth)
    {
        $myCourse = Course::where('user_id', $auth::user()->id)
            ->where('id', $id)
            ->get();

        return view('teacher.courses.edit', [
            'myCourse' => $myCourse[0]
        ]);
    }
}
