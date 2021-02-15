<?php
/**
 * User: dadicc
 * Date: 21.10.19
 * Time: 13:54
 */

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use App\{
    Course,
    CourseStudent,
    FinanceNomenclature,
    Nationality,
    Profiles,
    ProfileDoc,
    User
};
use App\Services\{Auth,Service1C};
use App\Validators\{
    CoursesGetCourseValidator,
    CoursesGetInfoValidator,
    CoursesGetInfoPostValidator,
    ProfileProfileIDPostValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB,Log,Mail};

class CoursesController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList( Request $request )
    {

        if( empty(Auth::user()->id) )
        {
            abort(404);
        }

        $aIds = [];
        if( Auth::user()->hasListenerCourseRole() )
        {
            $oCourseStudent = CourseStudent::
            with(['course' => function ($query) {
                $query->where('status', Course::STATUS_ACTIVE);
                $query->whereNull('deleted_at');
            }])->
            where('user_id',Auth::user()->id)->
            where('status',CourseStudent::STATUS_ACTIVE)->
            where('payed',CourseStudent::STATUS_PAYED_YES)->
            whereNull('deleted_at')->
            get();

            if( !empty($oCourseStudent) && (count($oCourseStudent) > 0) )
            {
                foreach( $oCourseStudent as $item )
                {
                    $aIds[] = $item->courses_id;
                }
            }
        }

        $oCourse = Course::
        whereNotIn('id',$aIds)->
        where('status',Course::STATUS_ACTIVE)->
        whereNull('deleted_at')->
        get();

        return view('courses.list',[
            'course'        => $oCourse,
            'courseStudent' => $oCourseStudent ?? null,
        ]);

    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCourse( Request $request )
    {

        // validation data
        $obValidator = CoursesGetCourseValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        $oCourse = Course::
        where('id',$request->input('course'))->
        first();

        if( Auth::user()->hasListenerCourseRole() )
        {
            $oCourseStudent = CourseStudent::
            with(['course' => function ($query){
                $query->where('status', Course::STATUS_ACTIVE);
                $query->whereNull('deleted_at');
            }])->
            where('courses_id',$request->input('course'))->
            where('user_id',Auth::user()->id)->
            where('status',CourseStudent::STATUS_ACTIVE)->
            //where('payed',CourseStudent::STATUS_PAYED_YES)->
            whereNull('deleted_at')->
            first();
        }

        if( empty($oCourse) )
        {
            abort(404);
        }

        return view('courses.course',[
            'course' => $oCourse,
            'courseStudent' => $oCourseStudent ?? null
        ]);


    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getInfo( Request $request )
    {

        // validation data
        $obValidator = CoursesGetInfoValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        $oCourse = Course::
        where('id',$request->input('course'))->
        first();

        if( empty($oCourse) || empty(Auth::user()->id) )
        {
            abort(404);
        }

        if( Auth::user()->hasRole('guest') && empty(Auth::user()->studentProfile->iin) )
        {
            // если гость или нет иин - отправляем на вод
            return redirect()->route('courseProfileId');
        }

        $language = [];
        if( !empty( $oCourse->language ) )
        {
            $language = explode(',',$oCourse->language);
        }

        $oCourseStudent = CourseStudent::
        where('courses_id',$request->input('course'))->
        where('user_id',Auth::user()->id)->
        whereRaw('YEAR(created_at) = ?',[date('Y')])->
        whereRaw('MONTH(created_at) = ?',[date('m')])->
        whereNull('deleted_at')->
        first();

        if( !empty($oCourseStudent) && ( $oCourseStudent->payed == CourseStudent::STATUS_PAYED_YES ) )
        {

            // если курс уже куплен
            return redirect()->back()->withErrors([ __('The course is already purchased') ]);
        }

        return view('courses.info',[
            'course'          => $oCourse,
            'courseStudent'   => $oCourseStudent ?? null,
            'language'        => $language
        ]);

    }


    /**
     * @param Request $request
     */
    public function getInfoPost( Request $request )
    {


        // validation data
        $obValidator = CoursesGetInfoPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return redirect()->back()->withErrors($obValidator->messages());
        }

        $oCourse = Course::
        where('id',$request->input('courses_id'))->
        whereNull('deleted_at')->
        first();

        $oFinanceNomenclature = FinanceNomenclature::
        where('code','00000007839')->
        first();

        if( empty(Auth::user()->id) || empty($oCourse) || empty($oFinanceNomenclature) )
        {
            abort(404);
        }

        // test balance
        if( Auth::user()->balance < $oCourse->cost )
        {
            return redirect()->back()->withErrors([ __('Not enough funds on balance') ]);
        }

        $oCourseStudent = CourseStudent::
        where('courses_id', $request->input('courses_id'))->
        where('user_id', Auth::user()->id)->
        whereRaw('YEAR(created_at) = ?',[date('Y')])->
        whereRaw('MONTH(created_at) = ?',[date('m')])->
        whereNull('deleted_at')->
        first();

        if( empty($oCourseStudent) )
        {
            $oCourseStudent = new CourseStudent();
            $oCourseStudent->fill( $request->all() );
            $oCourseStudent->user_id = Auth::user()->id;

            // send request to 1C
            //$bResponse = true;
            $bResponse = Service1C::pay(
                Auth::user()->studentProfile->iin,
                $oFinanceNomenclature->code,
                intval($oCourse->cost)
            );

            if( !empty($bResponse) )
            {

                // фиксируем участника курса
                $oCourse->training_group = intval($oCourse->training_group) + 1;

                $oCourseStudent->pay_method = CourseStudent::PAYMENT_METHOD_BALANCE;
                $oCourseStudent->payed = CourseStudent::STATUS_PAYED_YES;
                $oCourseStudent->status = CourseStudent::STATUS_ACTIVE;
                $oCourseStudent->save();
                $oCourse->save();

                // send mail
                Mail::send('emails.buy_course', [
                    'user_id' => Auth::user()->id,
                    'fio' => Auth::user()->studentProfile->fio ?? Auth::user()->name,
                    'service_code' => $oFinanceNomenclature->code,
                    'service_name' => $oFinanceNomenclature->name,
                    'cost' => intval($oCourse->cost),
                    'date' => date('d-m-Y H:i'),
                    'speciality' => Auth::user()->studentProfile->speciality->name ?? '',
                    'speciality_id' => Auth::user()->studentProfile->education_speciality_id ?? '',
                    'course' => Auth::user()->studentProfile->course ?? '',
                ], function ($message){
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to(['khojabayeva@miras.edu.kz'])->subject('Покупка курса');
                });

            } else {

                return redirect()->back()->withErrors([ __('Error when sending the request') ]);
            }

        } else {

            return redirect()->back()->withErrors([ __('This course has already been purchased') ]);
        }


        // stud redirect to courses list
        return redirect()->route('courseSuccessPay',[
            'courses_id' => $oCourse->id
        ]);

    }


    /**
     * @param Request $request
     */
    public function successPay( Request $request )
    {

        $oCourse = Course::
        where('id',$request->input('courses_id'))->
        whereNull('deleted_at')->
        first();

        return view('courses.successPay',[
            'course' => $oCourse
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cabinet( Request $request )
    {

        if( empty($balance = Auth::user()) || empty($balance = Auth::user()->studentProfile) )
        {
            abort(404);
        }

        $balance = Auth::user()->balance ?? 0;

        return view('courses.cabinet',[
            'balance' => $balance
        ]);

    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileId( Request $request )
    {

        $oProfile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($oProfile) )
        {
            return redirect()->back()->withErrors([ __('Profile not found') ]);
        }

        return view('courses.profileID');

    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function profileIdPost( Request $request )
    {

        // validation data
        $obValidator = ProfileProfileIDPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route('courseProfileId')->
            withErrors([__('Data not found')]);
        }

        $oProfile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($oProfile) ) { abort(404); }

        if($request->hasFile('front')) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_FRONT_ID, $request->file('front'));
        }
        if($request->hasFile('back')) {
            ProfileDoc::saveDocument(ProfileDoc::TYPE_BACK_ID, $request->file('back'));
        }

        $oProfile->front_id_photo = '1';
        $oProfile->back_id_photo = '1';
        $oProfile->save();

        return redirect()->route('courseProfileEdit');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileEdit( Request $request  )
    {

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }

        $front = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->first();
        $back = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_BACK_ID)->first();

        if ($profile->getOriginal('front_id_photo') != 0) {

            $type = 'kaz.id.*';
            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.public_path($front->getPathForDoc(ProfileDoc::TYPE_FRONT_ID, $front->filename) . $front->filename . ProfileDoc::EXT ).' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'';
            $SIDFront = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SIDFront);
            $SIDFront = json_decode($SIDFront);

            $type = 'kaz.id.*';
            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.public_path($back->getPathForDoc(ProfileDoc::TYPE_BACK_ID, $back->filename) . $back->filename . ProfileDoc::EXT ).' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type;
            $SIDBack = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SIDBack);
            $SIDBack = json_decode($SIDBack);

            if(!empty($SIDFront->str)) {
                foreach($SIDFront->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {
                return redirect()->route('courseProfileId')->withErrors([__("Error reading the front side of the ID, please try again")]);
            }

            if(!empty($SIDFront->img->photo)) {
                $SID['face'] = $SIDFront->img->photo;
            }

            if(!empty($SIDBack->str)) {
                foreach($SIDBack->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {
                return redirect()->route('courseProfileId')->withErrors([__("Error reading the back side of the ID, please try again")]);
            }

            $SID['init'] = 1;
            $SID = (object) $SID;
            //print_r($SID);

            if(isset($SID->surname)) $SID->fio = $SID->surname;
            if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
            if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;

            if(empty($SID->inn) || empty($SID->full_mrz) ||  !strpos($SID->full_mrz, $SID->inn) ) {
                return redirect()->route('courseProfileId')->withErrors([__("Mismatch between the back and front sides of the ID")]);
            }

            //checking expiration date
            if(empty($SID->expiry_date)) {
                return redirect()->route('courseProfileId')->withErrors([__("Can not read the expiration date")]);
            }
            if(strtotime($SID->expiry_date) <= time() ) {
                return redirect()->route('courseProfileId')->withErrors([__("Document is out of date")]);
            }

            if(isset($SID->face)) {
                $faceImgName = $SID->inn . str_random(5) . '.jpg';
                \File::put(public_path('images/uploads/faces/') . $faceImgName, base64_decode($SID->face));
                $profile->faceimg = $faceImgName;
            }


            $profile->user_id = Auth::user()->id;
            $profile->iin = $SID->inn;
            $profile->fio = $SID->fio;
            if(isset($SID->birth_date)) $profile->bdate = strtotime($SID->birth_date);
            if(isset($SID->number)) $profile->docnumber = $SID->number;
            if(isset($SID->issue_authority)) $profile->issuing = $SID->issue_authority;
            if(isset($SID->issue_date)) $profile->issuedate = strtotime($SID->issue_date);
            if(isset($SID->expiry_date)) $profile->expire_date = strtotime($SID->expiry_date);
            if( isset($inputs['nationality']) ) $profile->nationality = $SID->nationality;
            $profile->pass = 0;

            if($SID->gender_mrz == 'M') {
                $profile->sex = 1;
            } else {
                $profile->sex = 0;
            }
            $profile->save();

            if(isset($SID->name)) {
                $user = User::where('id', Auth::user()->id)->first();
                $user->name = $SID->name;
                $user->save();
            }

            // set role
            Auth::user()->setRole('listener_course');
            Auth::user()->unsetRole('guest');
            if( !empty(Auth::user()) && Auth::user()->hasRole('guest') )
            {
                Service1C::registration($profile->iin, $profile->fio, $profile->sex, $profile->bdate);
            }

        } else {

            return redirect()->route('courseProfileId')->withErrors([__("Error reading the front side of the ID, please try again")]);

        }

        return redirect()->route('getCoursesList');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileIdManual( Request $request )
    {

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $profile->registration_step = Profiles::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL;
        $profile->save();

        $nationalityList = Nationality::get();

        if( !empty($profile->iin) && !empty($profile->fio) )
        {
            return redirect()->route('getCoursesList');
        }

        return view('courses.profileIDManual',[
            'profile'         => $profile,
            'nationalityList' => $nationalityList
        ]);

    }


    /**
     * @param Request $request
     * @return string
     */
    public function profileIdManualPost( Request $request )
    {

        $inputs = $request->all();

        $alien = ( !empty($inputs['alien']) && ( $inputs['alien'] == 'true' ) ) ? true : false;

        if(strtotime($request['expire_date']) <= time() && !$alien) {
            $result['status'] = 'fail';
            $result['text'] = __("Document is out of date");
            return json_encode($result);
        }

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $profile->front_id_photo = '0';
        $profile->back_id_photo = '0';

        $profile->user_id = Auth::user()->id;
        if(isset($inputs['iin'])) $profile->iin = $inputs['iin'];
        if(isset($inputs['fio'])) $profile->fio = $inputs['fio'];
        if(isset($inputs['bdate'])) $profile->bdate = strtotime($inputs['bdate']);
        if(isset($inputs['docnumber'])) $profile->docnumber = $inputs['docnumber'];
        if(isset($inputs['issuing'])) $profile->issuing = $inputs['issuing'];
        if(isset($inputs['issuedate'])) $profile->issuedate = strtotime($inputs['issuedate']);
        if(isset($inputs['expire_date'])) $profile->expire_date = strtotime($inputs['expire_date']);
        if(isset($inputs['nationality']) ) $profile->nationality_id = $inputs['nationality'];
        $profile->alien = $alien;
        $profile->pass = 0;
        if( !empty($inputs['sex']) && ($inputs['sex'] == Profiles::GENDER_MALE) )
        {
            $profile->sex = Profiles::GENDER_MALE;
        } else {
            $profile->sex = Profiles::GENDER_FEMALE;
        }

        $profile->user_approved = 1;
        $profile->registration_step = Profiles::REGISTRATION_STEP_EMAIL;
        $profile->save();

        if(isset($profile->fio)) {
            $aName = explode(' ', $profile->fio);
            if( !empty($aName[1]) )
            {
                $user = User::where('id', Auth::user()->id)->first();
                $user->name = $aName[1];
                $user->save();
            }
        }

        // set role
        Auth::user()->setRole('listener_course');
        Auth::user()->unsetRole('guest');
        if( !empty(Auth::user()) && Auth::user()->hasRole('guest') )
        {
            Service1C::registration($profile->iin, $profile->fio, $profile->sex, $profile->bdate);
        }

        $result['status'] = 'success';

        return json_encode($result);

    }



}