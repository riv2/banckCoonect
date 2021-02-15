<?php

namespace App\Http\Controllers;

use App\Course\Course;
use App\Teacher\ProfileTeacher;
use App\ProfileDoc;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Mockery\Exception;

class ProfileTeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
//    public function index()
//    {
//        //
//    }

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teacher\ProfileTeacher  $profileTeacher
     * @return \Illuminate\Http\Response
     */
    public function show(ProfileTeacher $profileTeacher)
    {
        $user = Auth::user();
        $profileTeacher = ProfileTeacher::where('user_id', $user->id)
            ->get()
            ->toArray();

        return view('teacher.profile', [
            'profileTeacher' => $profileTeacher
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Teacher\ProfileTeacher  $profileTeacher
     * @return \Illuminate\Http\Response
     */
    public function edit(ProfileTeacher $profileTeacher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teacher\ProfileTeacher  $profileTeacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProfileTeacher $profileTeacher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Teacher\ProfileTeacher  $profileTeacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProfileTeacher $profileTeacher)
    {
        //
    }


    /*-----------------------------------------------*/

    public function index()
    {
        $teacher = Auth::user();
        $profile = ProfileTeacher::where('user_id', '=', Auth::user()->id)->first();

        if(empty($profile->id)) {
            return redirect()->route('teacherProfileID');
        } elseif(empty($profile->fio)) {
            return redirect()->route('teacherProfileEdit');
        }

        return view('pages.profile', compact('profile', 'teacher'));
    }

    public function profileID()
    {
        return view('teacher.profileID');
    }

    public function profileIDPost(Request $request)
    {
//        dd($request->toArray());
        $data =  \Input::except(array('_token')) ;

        $inputs = $request->all();

        $profileTeacher = new ProfileTeacher();

        $profileTeacher->user_id = Auth::user()->id;

        ProfileDoc::saveDocument('front_id_photo', $request->file('front'));
        ProfileDoc::saveDocument('back_id_photo', $request->file('back'));

        $profileTeacher->save();

        return redirect()->route('teacherProfileEdit');
    }

    public function profileEdit()
    {

        $user = Auth::user();


            $profileTeacher = ProfileTeacher::where('user_id', '=', Auth::user()->id)->first();
            $front = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->first();
            $back = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_BACK_ID)->first();

            $type = 'kaz.id.*';
            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.public_path($front->getPathForDoc($front::TYPE_FRONT_ID, $front->filename) . $front->filename . ProfileDoc::EXT ).' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'';
            $SIDFront = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SIDFront);
            $SIDFront = json_decode($SIDFront);

            $type = 'kaz.id.*';
            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.public_path($back->getPathForDoc(ProfileDoc::TYPE_BACK_ID, $back->filename) . $back->filename . ProfileDoc::EXT ).' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'';
            $SIDBack = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SIDBack);
            $SIDBack = json_decode($SIDBack);

            if(!empty($SIDFront->str)) {
                foreach($SIDFront->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            }

            if(!empty($SIDBack->str)) {
                foreach($SIDBack->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            }
            $SID['init'] = 1;
            $SID = (object) $SID;
            //print_r($SID);

            if(isset($SID->surname)) $SID->fio = $SID->surname;
            if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
            if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;

            return view('teacher.profileEdit', compact('profile', 'SID', 'SIDFront', 'SIDBack'));

    }

    public function profileEditPost(Request $request)
    {
//dd($request);
        $data =  \Input::except(array('_token')) ;

        $inputs = $request->all();

        $rule = [
            'iin' => 'required|numeric|min:12',
            'fio' => 'required|min:2',
            'bdate' => 'required|min:2',
            'docnumber' => 'required|min:2',
            'issuing' => 'required|min:2',
            'issuedate' => 'required|min:2',
            'mobile' => 'required|min:7'
        ];
        $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $profileTeacher = ProfileTeacher::where('user_id', '=', Auth::user()->id)->first();

        $profileTeacher->user_id = Auth::user()->id;
        $profileTeacher->iin = $inputs['iin'];
        $profileTeacher->fio = $inputs['fio'];
        $profileTeacher->bdate = $inputs['bdate'];
        $profileTeacher->docnumber = $inputs['docnumber'];
        $profileTeacher->issuing = $inputs['issuing'];
        $profileTeacher->issuedate = $inputs['issuedate'];
        $profileTeacher->mobile = $inputs['mobile'];


        $photoName = time().'.'.$request->photo->getClientOriginalExtension();
        $request->photo->move(public_path('avatars'), $photoName);
        $profileTeacher->photo = $photoName;


//        $photo = $request->file('photo') ? $request->file('photo')->store('storage/photos', 'public') : null;
//        dd($photo);

        if($inputs['doctype'] == 'pass') {
            $profileTeacher->pass = 1;
        } else {
            $profileTeacher->pass = 0;
        }
        if($inputs['sex'] == 'man') {
            $profileTeacher->sex = 1;
        } else {
            $profileTeacher->sex = 0;
        }

        $profileTeacher->save();



        return redirect()->route('profileTeacherShow');
    }

    public function coursesAddForm()
    {
        return view('teacher.courses.add');
    }

    public function coursesAddCreate(Request $request, Course $course)
    {
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
