<?php

namespace App\Http\Controllers;

use App\{
    AgitatorUsers,
    BcApplications,
    FinanceNomenclature,
    MgApplications,
    Nationality,
    PayDocument,
    Profiles,
    ProfileDoc,
    ProfileDocsType,
    Speciality,
    User
};
use App\Services\{Avatar,DocxHelper,GenerateStudentContract,RegistrationHelper,PhpOfficeHelper,Service1C,StepByStep};
use App\Teacher\TeacherSkill;
use App\UserApplication;
use Chumper\Zipper\Zipper;
use Illuminate\Http\Request;
use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\{File,Image,Log,Response,Session,Validator};
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Profiler\Profile;
use WrkLst\DocxMustache\DocxMustache;
use App\Validators\{
    BcApplicationValidator,
    ProfileAddAgitatorPostValidator,
    ProfileEditWorkPlaceValidator,
    ProfilePaymentPostValidator,
    ProfileProfileEmailPostValidator,
    ProfileProfileIDPostValidator,
    RegistrationProfileFamilyStatusValidator
};
use App\Models\StudentRequest\StudentRequestType;
use App\Models\StudentRequest\StudentRequest;


class ProfileController extends Controller
{


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
    	$user = Auth::user();
        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        if(empty($profile->registration_step) || $profile->registration_step != 'finish' || empty($profile->speciality))
        {
            if (empty($profile->id) || empty($profile->mobile)) {
                return redirect()->route('userProfileID');
            }
        }
        /*} elseif(empty($profile->phone)) {
            return redirect()->route('userProfileEdit');
        }*/
        $application = $this->docsNeedToUploadGetList();

        $docType = ProfileDocsType::whereNull('hidden')->get();

        $requestTypes = StudentRequestType::get();

        $mgApplications = MgApplications::
        where('user_id',Auth::user()->id)->
        first();

        $bcApplications = BcApplications::
        where('user_id',Auth::user()->id)->
        first();

    	return view('pages.profile', compact(
    	    'profile',
            'user',
            'application',
            'docType',
            'bcApplications',
            'mgApplications',
            'requestTypes'
        ));

    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function editProfile(Request $request)
    {

        // validation data
        $obValidator = ProfileEditWorkPlaceValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route('userProfile')->
            withErrors([__('Workplace input error')]);
        }

        if($request->input('current_password') && $request->input('password'))
        {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|confirmed'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
        }

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();
        $profile->family_status = $request->input('family_status') ?? $profile->family_status;
        $profile->workplace = $request->input('workplace');
        $profile->save();

        if($request->input('current_password') && $request->input('password')) {
            $newPassword = $request->input('password');
            $user = \App\Services\Auth::user();
            $user->password = bcrypt($newPassword);

            if ($user->keycloak == true) {
                $user->keycloak = false;
            }

            $user->save();
        }

        \Session::flash('flash_message', __('Changes saved'));
        return redirect()->route('userProfile')->with('withoutBack', true);

    }


    /**
     * Register step 1
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileID(Request $request)
    {

        $oProfile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($oProfile) ) { abort(404); }
        if( $oProfile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_USER_PROFILE_ID ) )
        {
            return $oProfile->getRegisterRoute( $oProfile->registration_step );
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_USER_PROFILE_ID );

        return view('pages.profileID');

    }


    /**
     * Register step 1
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileIDPost(Request $request)
    {

        // validation data
        $obValidator = ProfileProfileIDPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route('userProfileID')->
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

        return redirect()->route('userProfileEdit');

    }


    /**
     * Register step 1
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileEdit()
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
                return redirect()->route('userProfileID')->withErrors([__("Error reading the front side of the ID, please try again")]);
            }

            if(!empty($SIDFront->img->photo)) {
                $SID['face'] = $SIDFront->img->photo;
            }

            if(!empty($SIDBack->str)) {
                foreach($SIDBack->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {
                return redirect()->route('userProfileID')->withErrors([__("Error reading the back side of the ID, please try again")]);
            }

            $SID['init'] = 1;
            $SID = (object) $SID;
            //print_r($SID);

            if(!empty($SID->inn))
            {
                $profilesCount = Profiles::leftJoin('users', 'users.id', '=', 'profiles.user_id')
                    ->whereNull('users.deleted_at')
                    ->where('profiles.iin', $SID->inn)
                    ->first();

                if( !empty($profilesCount) && ($profilesCount->user_id != Auth::user()->id) )
                {
                    return redirect()->route('userProfileID')->withErrors([__("IIN already exists")]);
                }
            }

            if(isset($SID->surname)) $SID->fio = $SID->surname;
            if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
            if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;
            
            if(empty($SID->inn) || empty($SID->full_mrz) ||  !strpos($SID->full_mrz, $SID->inn) ) {
                return redirect()->route('userProfileID')->withErrors([__("Mismatch between the back and front sides of the ID")]);
            }

            //checking expiration date
            if(empty($SID->expiry_date)) {
                return redirect()->route('userProfileID')->withErrors([__("Can not read the expiration date")]);
            }
            if(strtotime($SID->expiry_date) <= time() ) {
                return redirect()->route('userProfileID')->withErrors([__("Document is out of date")]);
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

            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_EMAIL );

        } else {

            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL );
            return redirect()->route(Profiles::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL);

        }

        return view('pages.profileEdit', compact('profile'));

    }


    /**
     * Register step 1
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileIDManual()
    {

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_USER_PROFILE_ID_MANUAL );

        $nationalityList = Nationality::get();

        return view('pages.profileIDManual', compact('profile', 'nationalityList'));

    }


    /**
     * Register step 1
     * @param Request $request
     * @return string
     */
    public function profileIDManualPost(Request $request)
    {

        $inputs = $request->all();

        $alien = ( !empty($inputs['alien']) && ( $inputs['alien'] == 'true' ) ) ? true : false;

        if(strtotime($request['expire_date']) <= time() && !$alien) {
            $result['status'] = 'fail';
            $result['text'] = __("Document is out of date");
            return json_encode($result);
        }

        $iin = $request->input('iin', null);

        if($iin)
        {
            $profilesCount = Profiles::leftJoin('users', 'users.id', '=', 'profiles.user_id')
                ->whereNull('users.deleted_at')
                ->where('profiles.iin', $iin)
                ->first();

            if( !empty($profilesCount) && ( $profilesCount->user_id != Auth::user()->id ) )
            {
                $result['status'] = 'fail';
                $result['text'] = __("IIN already exists");
                return json_encode($result);
            }
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
        $profile->save();

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_EMAIL );

        if(isset($profile->fio)) {
            $aName = explode(' ', $profile->fio);
            if( !empty($aName[1]) )
            {
                $user = User::where('id', Auth::user()->id)->first();
                $user->name = $aName[1];
                $user->save();
            }
        }

        $result['status'] = 'success';

        return json_encode($result);

    }


    /**
     * Register step 2
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileEmail()
    {

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_EMAIL ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_EMAIL );

        return view('pages.profileEmail');

    }


    /**
     * Register step 2
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function profileEmailPost(Request $request)
    {

        $oProfile = Profiles::where('user_id', Auth::user()->id)->first();
        $oUser = User::where('id',Auth::user()->id)->first();
        if( empty($oProfile) ) { abort(404); }

        if( !empty($oUser) && !empty($oProfile) && $request->has('email') &&
            ( $request->input('email') != '' )
        )
        {
            $oUser->email = $request->input('email');
            $oUser->save();
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_AFTER_ID );

        return redirect()->route(Profiles::REGISTRATION_STEP_AFTER_ID );

    }


    /**
     * Register step 3
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function referralSource()
    {
        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_REFERRAL_SOURCE ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

        return view('student.referral_source');
    }


    /**
     * Register step 3
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function referralSourcePost(Request $request)
    {

        $referral = $request->input('referral', null);
        $user = \App\Services\Auth::user();
        $profile = Auth::user()->studentProfile;

        if( !$referral || empty($user) || empty($profile) )
        {
            abort(404);
        }

        if($referral == 'At the invitation of the agitator')
        {
            $user->referral_name = $request->input('referral_agitator', null);
        }

        if($referral == 'Other')
        {
            $referral = $request->input('other', null);
        }

        $user->referral_source = $referral;
        $user->save();

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_AFTER_ID );

        return redirect()->route(Profiles::REGISTRATION_STEP_AFTER_ID );

    }


    /**
     * Register step 4
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function afterUserIDSend()
    {

        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_AFTER_ID ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

        $application = '';

        if(\App\Services\Auth::user()->studentProfile->education_speciality_id)
        {
            $codeChar = \App\Services\Auth::user()->studentProfile->speciality->code_char;

            if($codeChar == 'b')
            {
                $application = 'bachelor';
            }

            if($codeChar == 'm')
            {
                $application = 'master';
            }
        }

        if(!$application) {
            if (\App\Services\Auth::user()->bcApplication) {
                $application = 'bachelor';
            }

            if (\App\Services\Auth::user()->mgApplication) {
                $application = 'master';
            }
        }

        if ($application) {

            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_SPECIALITY_SELECT . "?$application" );
            return redirect()->route(Profiles::REGISTRATION_STEP_SPECIALITY_SELECT, ['application' => $application]);
        }

        return view('pages.afterID');

    }


    /**
     * Register step 6
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function setEducationLanguage($application)
    {

        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');
        $profile = Profiles::where('user_id', Auth::user()->id)->first();

        if(!$config && !empty($profile) )
        {
            abort(404);
        }

        if( $profile->isRedirectToRegisterStep( Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE ) )
        {
            return $profile->getRegisterRoute( $profile->registration_step );
        }

        if( Profiles::getRegisterPriority( Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE ) > Profiles::getRegisterPriority($profile->registration_step) )
        {
            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_STUDENT_EDUCATION_LANGUAGE );
        }

        /*
        if(\App\Services\Auth::user()->studentProfile->education_lang)
        {
            return redirect()->route(StepByStep::nextRouteAfter(
                'studentEducationLanguage',
                $config
            ), ['application' => $application]);
        }
        */

        return view('student.education_language');
    }


    /**
     * Register step 6
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function setEducationLanguagePost(Request $request, $application)
    {

        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');
        $profile = Profiles::where('user_id', Auth::user()->id)->first();

        if(!$config && !empty($profile) )
        {
            abort(404);
        }

        if(\App\Services\Auth::user()->studentProfile->education_lang)
        {
            return redirect()->route(StepByStep::nextRouteAfter('studentEducationLanguage', $config), ['application' => $application]);
        }

        $validator = Validator::make($request->all(), [
            'education_lang' => [Rule::in(['ru', 'kz', 'en'])]
        ]);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        Auth::user()->studentProfile->education_lang = $request->input('education_lang');
        Auth::user()->studentProfile->save();

        return redirect()->route(StepByStep::nextRouteAfter('studentEducationLanguage', $config), ['application' => $application]);
    }


    /**
     * Register step 7
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function studyForm($application)
    {

        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');
        $profile = \App\Services\Auth::user()->studentProfile;

        if( !$config && !empty($profile) )
        {
            abort(404);
        }

        if( Profiles::getRegisterPriority( Profiles::REGISTRATION_STEP_STUDENT_STUDY_FORM ) > Profiles::getRegisterPriority($profile->registration_step) )
        {
            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_STUDENT_STUDY_FORM );
        }

        if($profile->speciality->code_char == 'm')
        {
            $profile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
            $profile->save();
            return redirect()->route(StepByStep::nextRouteAfter('studyForm', $config), ['application' => $application]);
        }

        if($profile->education_study_form)
        {
            return redirect()->route(StepByStep::nextRouteAfter('studyForm', $config), ['application' => $application]);
        }

        return view('student.education_study_form');
    }


    /**
     * Register step 7
     * @param Request $request
     * @param $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function studyFormPost(Request $request, $application)
    {

        $config = $application == 'master' ? 'mg_application' : ($application == 'bachelor' ? 'bc_application' : '');
        $profile = \App\Services\Auth::user()->studentProfile;

        if( !$config && !empty($profile) )
        {
            abort(404);
        }

        if($profile->education_study_form)
        {
            return redirect()->route(StepByStep::nextRouteAfter('studyForm', $config), ['application' => $application]);
        }

        $studyForm = $request->input('education_study_form', null);
        if(!in_array( $studyForm, [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Profiles::EDUCATION_STUDY_FORM_EVENING,
            Profiles::EDUCATION_STUDY_FORM_ONLINE
        ]))
        {
            abort(400);
        }

        $profile->education_study_form = $studyForm;
        $profile->save();

        return redirect()->route(StepByStep::nextRouteAfter('studyForm', $config), ['application' => $application]);
    }


    /**
     * Register step 12
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function addAgitator(Request $request)
    {

        return view('student.add_agitator');
    }


    /**
     * Register step 12
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function withoutAgitator(Request $request)
    {

        $oUser = User::
        where('id',Auth::user()->id)->
        first();

        if( empty($oUser) )
        {
            return redirect()->route(Profiles::REGISTRATION_STEP_AGITATOR)->withErrors([__('Data not found')]);
        }

        if( Auth::user()->hasRole('guest') )
        {

            // user registration in 1C
            $bService1CRes = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);

            if( empty($bService1CRes) )
            {
                return redirect()->route(Profiles::REGISTRATION_STEP_AGITATOR)->withErrors([__('Data not found')]);
            }

            // fix registration step
            $profile = Auth::user()->studentProfile;
            $profile->registration_step = Profiles::REGISTRATION_STEP_PAYMENT;
            $profile->save();

            return redirect()->route(Profiles::REGISTRATION_STEP_PAYMENT);

        }

        return redirect()->back();

    }


    /**
     * Register step 12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAgitator(Request $request)
    {

        $oProfiles = null;
        $sFio = null;
        $sPhone = null;
        if( $request->has('fio') && !empty($request->input('fio')) )
        {

            $sFio = trim($request->input('fio'));

            $oProfiles = Profiles::
            where('fio','like','%'.$sFio.'%')->
            where('user_id','!=',Auth::user()->id)->
            whereIn('agitator_registration_step',[Profiles::AGITATOR_REGISTRATION_STEP_FINISH,Profiles::REGISTRATION_STEP_FINISH_OLD])->
            first();

        }
        if( $request->has('phone') && !empty($request->input('phone')) )
        {

            $sPhone = trim($request->input('phone'));

            $oProfiles = Profiles::
            where('mobile','like','%'.$sPhone.'%')->
            where('user_id','!=',Auth::user()->id)->
            whereIn('agitator_registration_step',[Profiles::AGITATOR_REGISTRATION_STEP_FINISH,Profiles::REGISTRATION_STEP_FINISH_OLD])->
            first();

        }

        if( empty($oUser) && empty($oProfiles) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Agitator not found')
            ]);
        }


        if( !empty($oUser) )
        {
            return \Response::json([
                'status'  => true,
                'data' => [
                    'fio'    => $oUser->name,
                    'phone'  => $oUser->phone,
                    'avatar' => "/images/uploads/faces/default.png"
                ]
            ]);
        }


        if( !empty($oProfiles) )
        {
            return \Response::json([
                'status'  => true,
                'data' => [
                    'fio'    => $oProfiles->fio,
                    'phone'  => $oProfiles->mobile,
                    'avatar' => $oProfiles->faceimg ? \App\Services\Avatar::getStudentFacePublicPath($oProfiles->faceimg) : "/images/uploads/faces/default.png"
                ]
            ]);
        }

    }


    /**
     * Register step 12
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAgitatorPost(Request $request)
    {

        // validation data
        $obValidator = ProfileAddAgitatorPostValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()) || empty(Auth::user()->studentProfile) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error input data')
            ]);
        }

        $oUser = User::
        with('studentProfile')->
        where('id',Auth::user()->id)->
        first();

        $oAgitator = User::
        select([
            'users.id as id',
            'profiles.user_id as user_id',
            'profiles.fio',
            'profiles.mobile',
        ])->
        leftJoin('profiles', 'profiles.user_id', '=', 'users.id')->
        where('profiles.fio',$request->input('fio'))->
        orWhere('profiles.mobile',$request->input('phone'))->
        first();

        if( empty($oUser) || empty($oAgitator) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error adding agitator')
            ]);
        }

        // user registration in 1C
        $bService1CRes = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);

        if( empty($bService1CRes) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error adding agitator')
            ]);
        }

        // set referral
        $oUser->referral_name = $request->input('fio');
        $oUser->save();

        // set agitator user
        $oAgitatorUsers = new AgitatorUsers();
        $oAgitatorUsers->fill([
            'user_id' => $oAgitator->id,
            'stud_id' => $oUser->id,
            'status'  => AgitatorUsers::STATUS_PROCESS
        ]);
        $oAgitatorUsers->save();

        // fix registration step
        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_PAYMENT );

        return \Response::json([
            'status'  => true
        ]);

    }


    /**
     * Register step 13
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payment(Request $request)
    {

        $iCost = 5000;

        // получаем тек баланс юзера
        $mResponseBalance = Service1C::getBalance( Auth::user()->studentProfile->iin );
        if (env('API_1C_EMULATED', false)) {
            $mResponseBalance = $iCost;
        }

        return view('student.register_payment',[
            'cost'    => $iCost,
            'balance' => intval($mResponseBalance)
        ]);

    }


    /**
     * Register step 13
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function paymentPost(Request $request)
    {


        // validation data
        $obValidator = ProfilePaymentPostValidator::make( $request->all() );
        if( $obValidator->fails() || empty(Auth::user()) || empty(Auth::user()->studentProfile) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error input data')
            ]);
        }

        $oUser = User::
        with('studentProfile')->
        where('id',Auth::user()->id)->
        first();

        if( empty($oUser) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error payment')
            ]);
        }

        $iCost = 5000;

        // получаем номенклатуру
        $oFinanceNomenclature = FinanceNomenclature::where('id',5)->first();
        if( empty($oFinanceNomenclature) ){ abort(404); }


        // получаем тек баланс юзера
        $mResponseBalance = Service1C::getBalance( $oUser->studentProfile->iin );
        if( empty($mResponseBalance) || ( $mResponseBalance < $iCost ) )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('You do not have enough balance')
            ]);
        }

        // фиксируем фин операцию у нас
        $oPayDocument = new PayDocument();
        $oPayDocument->order_id       = rand(100000,999999);
        $oPayDocument->user_id        = Auth::user()->id;
        $oPayDocument->amount         = $iCost;
        $oPayDocument->balance_before = $mResponseBalance;
        $oPayDocument->status         = PayDocument::STATUS_PROCESS;
        $oPayDocument->type           = PayDocument::TYPE_REGISTRATION;
        $oPayDocument->complete_pay   = 1;
        $oPayDocument->save();


        // оплата за регистрацию
        $bService1CRes = Service1C::pay($oUser->studentProfile->iin, $oFinanceNomenclature->code ?? '', $iCost);
        if( empty($bService1CRes) )
        {

            $oPayDocument->status = PayDocument::STATUS_FAIL;
            $oPayDocument->save();
            return \Response::json([
                'status'  => false,
                'message' => 'Payment error'
            ]);
        }

        // платеж прошел, меняем статус фин операции
        $oPayDocument->status = PayDocument::STATUS_SUCCESS;
        $oPayDocument->save();

        // set role
        $oUser->studentProfile->education_status = Profiles::EDUCATION_STATUS_MATRICULANT;
        $oUser->studentProfile->save();
        Auth::user()->setRole('client');
        Auth::user()->unsetRole('guest');
        Auth::user()->refreshSearchAdminMatriculants();
        Auth::user()->updateGuestSearchCache();

        // fix registration step
        $profile = Auth::user()->studentProfile;
        $profile->registration_step = Profiles::REGISTRATION_STEP_FINISH_OLD;
        $profile->save();

        // определяем есть ли у этого студика агитатор
        $oAgitatorUsers = AgitatorUsers::
        where('stud_id',Auth::user()->id)->
        first();
        if( !empty($oAgitatorUsers) )
        {

            // фиксируем платеж
            $oAgitatorUsers->cost   = $iCost;
            $oAgitatorUsers->status = AgitatorUsers::STATUS_OK;
            $oAgitatorUsers->save();

        }

        return \Response::json([
            'status'  => true
        ]);

    }


    public function registerFinish(Request $request)
    {

        return view('student.register_finish');
    }


    /**
     * @return false|string
     */
    public function profileApprove()
    {
        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $profile->user_approved = 1;
        $profile->save();

        $result['status'] = 'success';

        return json_encode($result);
    }


    /**
     * @param Request $request
     * @return false|string
     */
    public function profileMobile(Request $request)
    {   
        $data =  \Input::except(array('_token')) ;

        $debugPassword = env('APP_DEBUG_PASSWORD', null);
        if($debugPassword && $request->input('mobile') == $debugPassword)
        {
            Auth::user()->setIgnoreConfirmMobile();
            return json_encode([
                'status' => 'redirect',
                'message' => ''
            ]);
        }

        $rule = ['mobile' => 'required|min:7'];
        $validator = \Validator::make($data,$rule);

        if ($validator->fails())
        {
            return json_encode([
                'status' => 'fail',
                'message' => __('Invalid mobile phone format')
            ]);
        }

        $phoneNumber = preg_replace('~[^0-9\+]+~','', $request->input('mobile'));

        $countPhone = Profiles
            ::leftJoin('users', 'users.id', '=', 'profiles.user_id')
            ->where('profiles.mobile', 'like', '%' . substr($phoneNumber, 2))
            ->where('users.id', '!=', \App\Services\Auth::user()->id)
            ->where('profiles.mobile_confirm', true)
            ->whereNull('users.deleted_at')
            ->count();

        if($countPhone > 0)
        {
            return json_encode([
                'status' => 'fail',
                'message' => __('Number is already registered')
            ]);
        }

        \App\Services\Auth::user()->sendPhoneConfirmCode($phoneNumber);
        $result['status'] = 'success';

        return json_encode($result);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileMobileApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json([
                'status' => 'fail'
            ]);
        }

        $phoneConfirm = \App\Services\Auth::user()->checkPhoneConfirmCode($request->input('code'));

        if(!$phoneConfirm)
        {
            return Response::json([
                'status' => 'fail'
            ]);
        }

        $profile = Profiles::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $profile->mobile = $phoneConfirm->phone_number;
        $profile->mobile_confirm = true;
        //$profile->importFromMirasFull();

        //$redirect = route(Profiles::REGISTRATION_STEP_FAMILY_STATUS);
        $redirect = route('referralSource');

        if($profile->import_full == true)
        {
            $redirect = route('userProfileImport');
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_REFERRAL_SOURCE );

        return Response::json([
            'redirect' => $redirect
        ]);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileMobileDoubleApprove(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|numeric'
        ]);

        if($validator->fails())
        {
            return Response::json([
                'status' => 'fail',
                'message' => __('Wrong code from SMS message')
            ]);
        }

        $phoneConfirm = \App\Services\Auth::user()->checkPhoneConfirmCode($request->input('code'));

        if(!$phoneConfirm)
        {
            return Response::json([
                'status' => 'fail',
                'message' => __('Wrong code from SMS message')
            ]);
        }

        $profile = Profiles::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $profile->mobile = $phoneConfirm->phone_number;
        $profile->mobile_confirm = true;
        $profile->save();

        Profiles
            ::where('mobile', 'like', '%' . substr($phoneConfirm->phone_number, 2))
            ->where('user_id', '!=', \App\Services\Auth::user()->id)
            ->where('iin', '!=', $profile->iin)
            ->update(['mobile' => null, 'mobile_confirm' => false]);

        return Response::json([
            'status' => 'success'
        ]);
    }

    /**
     * @param Request $request
     */
    public function profileFamilyStatus(Request $request)
    {

        $oProfile = Profiles::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        if( !empty( $oProfile->family_status ) )
        {
            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_REFERRAL_SOURCE );
            // если есть адресс то переход на след страницу
            return redirect()->
            route( 'referralSource' );
        }
        return view('pages.profileFamilyStatus');

    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function profileFamilyStatusPost(Request $request)
    {

        // validation data
        $obValidator = RegistrationProfileFamilyStatusValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(Profiles::REGISTRATION_STEP_FAMILY_STATUS)->
            withErrors([__('Family input error')]);
        }

        $oProfile = Profiles::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($profile) ) { abort(404); }
        $oProfile->family_status = $request->input('family_status');
        $oProfile->save();

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_REFERRAL_SOURCE );

        return redirect()->route('referralSource');

    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function importResult()
    {
        $profile = Profiles
            ::where('user_id', \App\Services\Auth::user()->id)
            ->first();

        if(!$profile)
        {
            abort(404);
        }

        return view('student.profile_import_full', ['profile' => $profile]);
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function bcApplication()
    {
        $bcApplication = BcApplications::where('user_id', \App\Services\Auth::user()->id)->first();
        if(!$bcApplication)
        {
            $bcApplication = new BcApplications();
        }
        else
        {
            return redirect()->route(StepByStep::nextRouteAfter('bcApplication', 'bc_application'));
        }

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

    	//$fieldLists['nationality'] = DB::table('nationality_list')->orderBy('name')->get();
    	$fieldLists['citizenship'] = DB::table('country_list')->orderBy('id')->orderBy('code')->orderBy('name')->get();
    	$fieldLists['familyStatus'] = DB::table('family_status_list')->orderBy('name')->get();
        $fieldLists['regions'] = DB::table('regions')->orderBy('id')->orderBy('name')->get();
        $fieldLists['cities'] = DB::table('cities')->orderBy('id')->orderBy('name')->get();
    	
        /*$age = Carbon::parse($profile->bdate)->age;

        if( $age >= 18 && $age <= 29 && $profile->sex == 1 ) { // profile — man and 18-29 year
            $needMilitaryPhoto = true;
        } else {
            $needMilitaryPhoto = false;
        }

        if( $age < 23  ) { // profile — younger 23 years
            $needR063Photo = true;
        } else {
            $needR063Photo = false;
        }*/
        $needMilitaryPhoto = false;

    	return view('student.bc_application.main', compact(
    	    'profile',
            'fieldLists',
            'needMilitaryPhoto',
            'needR063Photo',
            'bcApplication'
            ));
    }


    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function bcApplicationPost(Request $request)
    {
        $validator = BcApplicationValidator::make($request->all());

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $application = BcApplications::where('user_id', \App\Services\Auth::user()->id)->first();
        if(!$application)
        {
            $application = new BcApplications();
        }else
        {
            return redirect()->route(StepByStep::nextRouteAfter('bcApplication', 'bc_application'));
        }

        $application->user_id = Auth::user()->id;
        $application->fill($request->all());
        $application->citizenship_id = \App\Services\Auth::user()->defaultCitizenshipId();
        //$application->syncResidenceRegistration($request->file('residenceregistration', null));
        //$application->syncMilitary($request->file('military', null));
        $application->syncR086($request->file('r086', null));
        $application->syncR063($request->file('r063', null));
        $application->syncAttEducation($request->file('atteducation', null));
        $application->syncNostrificationAttach($request->file('nostrificationattach', null));
        if($request->input('has_ent') == 'true')
        {
            $application->attachEnt();
        }
        $application->save();

        return redirect()->route(StepByStep::nextRouteAfter('bcApplication', 'bc_application'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function teacherAdditionalInfo()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        //$fieldLists['nationality'] = DB::table('nationality_list')->orderBy('name')->get();

        $fieldLists['citizenship'] = DB::table('country_list')->orderBy('id')->orderBy('code')->orderBy('name')->get();

        $fieldLists['familyStatus'] = DB::table('family_status_list')->orderBy('name')->get();

        $fieldLists['regions'] = DB::table('regions')->orderBy('id')->orderBy('name')->get();

        $fieldLists['cities'] = DB::table('cities')->orderBy('id')->orderBy('name')->get();

        /*$age = Carbon::parse($profile->bdate)->age;

        if( $age >= 18 && $age <= 29 && $profile->sex == 1 ) { // profile — man and 18-29 year
            $needMilitaryPhoto = true;
        } else {
            $needMilitaryPhoto = false;
        }

        if( $age < 23  ) { // profile — younger 23 years
            $needR063Photo = true;
        } else {
            $needR063Photo = false;
        }*/
        $needMilitaryPhoto = false;

        return view('pages.teacherAdditionalInfo', compact('profile', 'fieldLists', 'needMilitaryPhoto', 'needR063Photo'));
    }


    /**
     * @param Request $request
     */
    public function teacherAdditionalInfoPost(Request $request)
    {
        $userID = \Illuminate\Support\Facades\Auth::user()->id;
        $skills = $request['skills'];

        $skillsArray = explode(',', $skills);

        dd(array_values($skillsArray));

        TeacherSkill::firstOrNew($skillsArray);

        foreach ($skillsArray as $key => $value){
            $teacherSkill = new TeacherSkill();
            $teacherSkill->user_id = $userID;
            $teacherSkill->skill = $value;
            $teacherSkill->save();
        }



    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mgApplication()
    {
        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        //$fieldLists['nationality'] = DB::table('nationality_list')->orderBy('name')->get();
        $fieldLists['citizenship'] = DB::table('country_list')->orderBy('id')->orderBy('code')->orderBy('name')->get();
        $fieldLists['familyStatus'] = DB::table('family_status_list')->orderBy('name')->get();
        $fieldLists['regions'] = DB::table('regions')->orderBy('id')->orderBy('name')->get();
        $fieldLists['cities'] = DB::table('cities')->orderBy('id')->orderBy('name')->get();

        /*$age = Carbon::parse($profile->bdate)->age;

        if( $age >= 18 && $age <= 29 && $profile->sex == 1 ) { // profile — man and 18-29 year
            $needMilitaryPhoto = true;
        } else {
            $needMilitaryPhoto = false;
        }

        if( $age < 23  ) { // profile — younger 23 years
            $needR063Photo = true;
        } else {
            $needR063Photo = false;
        }*/
        $needMilitaryPhoto = false;

        return view('student.mg_application.main', compact(
            'profile',
            'fieldLists',
            'needR063Photo',
            'needMilitaryPhoto'
        ));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function mgApplicationPost(Request $request)
    {
        /*$validator = MgApplicationValidator::make($request->all());

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }*/

        $application = MgApplications::where('user_id', \App\Services\Auth::user()->id)->first();
        if(!$application)
        {
            $application = new MgApplications();
        }else
        {
            return redirect()->route(StepByStep::nextRouteAfter('mgApplication', 'mg_application'));
        }

        $application->user_id = Auth::user()->id;
        $application->fill($request->all());
        $application->citizenship_id = \App\Services\Auth::user()->defaultCitizenshipId();
        //$application->syncResidenceRegistration($request->file('residenceregistration', null));
        //$application->syncMilitary($request->file('military', null));
        $application->syncR086($request->file('r086', null));
        $application->syncR063($request->file('r063', null));
        $application->syncAttEducation($request->file('atteducation', null));
        $application->syncNostrificationAttach($request->file('nostrificationattach', null));
        $application->syncEngCertificate($request->file('eng_certificate_photo', null));
        $application->syncWorkBook($request->file('work_book', null));
        $application->save();

        if($request->input('publication')) {
            $publicationList = $request->input('publication');
            $files = $request->file('publication');
            foreach ($publicationList as $k => $item)
            {
                $publicationList[$k]['file'] = $files[$k]['file'];
            }

            $application->syncPublications($publicationList);
        }

        return redirect()->route(StepByStep::nextRouteAfter('mgApplication', 'mg_application'));
    }


    /**
     * @return object
     */
    public function docsNeedToUploadGetList()
    {
        $application = BcApplications::where('user_id', Auth::user()->id)->first();
        if(empty($application->id)) {
            $application = MgApplications::where('user_id', Auth::user()->id)->first();
            if(!empty($application->id)) {
                $application->type = 'mg';
            } else {
                $application = (object) [];
            }
        } else {
            $application->type = 'bc';
        }

        $docs = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->orderBy('created_at','desc')->get();

        $i = 0;
        $docList = [];
        foreach ($docs as $doc) {
            //$docList[$doc->doc_type] = $doc->filename;
            if( $doc->doc_type == ProfileDoc::TYPE_EDUCATION_CONTRACT )
            {
                if( $i < 5 )
                {
                    $aTempData = [];
                    $aTempData['filename'] = $doc->filename;
                    $aTempData['status'] = $doc->status;
                    $aTempData['path'] = $doc->getPathForDoc($doc->doc_type, $doc->filename);
                    $docList[$doc->doc_type][] = $aTempData;
                }
                $i++;
            } else {

                $docList[$doc->doc_type]['filename'] = $doc->filename;
                $docList[$doc->doc_type]['status'] = $doc->status;
                $docList[$doc->doc_type]['path'] = $doc->getPathForDoc($doc->doc_type, $doc->filename);
            }
        }

        $application->docs = (object) $docList;

        return $application;
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function docsNeedToUpload()
    {
        $application = $this->docsNeedToUploadGetList();
        return view('student.docsNeedToUpload', compact('application'));
    }


    /**
     * @param Request $request
     * @return array
     */
    public function docsNeedToUploadPost(Request $request)
    {
        
        $inputs = $request->all();

        if ($inputs['type'] == 'bc'){
            $application = BcApplications::where('user_id', Auth::user()->id)->first();
        } else {
            $application = MgApplications::where('user_id', Auth::user()->id)->first();
        }

        if ( empty(Auth::user()->id) || empty($application) ){
            return redirect()->back()->withErrors([__("Data not found")]);
        }

        /*if ($inputs['doc_type'] == ProfileDoc::) {
            $application->syncResidenceRegistration($request->file('residenceregistration', null));
        }*/

        $docsChange = false;
        $singleFile = true;
        if(count($request->file('files')) > 1) {
            $singleFile = false;
        }
        $profile = \App\Services\Auth::user()->studentProfile;

        if ($inputs['doc_type'] == ProfileDoc::TYPE_MILITARY) {
            $docsChange = isset($profile->doc_military);
            $application->syncMilitary();
            $docType = ProfileDoc::TYPE_MILITARY;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_R086) {
            $docsChange = isset($profile->doc_r086);
            $application->syncR086();
            $docType = ProfileDoc::TYPE_R086;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_R063) {
            $docsChange = isset($profile->doc_r063);
            $application->syncR063();
            $docType = ProfileDoc::TYPE_R063;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_DIPLOMA) {
            $docsChange = isset($profile->diploma_photo);
            $docType = ProfileDoc::TYPE_DIPLOMA;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_ATTEDUCATION) {
            $docsChange = isset($profile->doc_atteducation);
            $application->syncAttEducation();
            $docType = ProfileDoc::TYPE_ATTEDUCATION;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_NOSTRIFICATION) {
            $docsChange = isset($profile->doc_nostrification);
            $application->syncNostrificationAttach();
            $docType = ProfileDoc::TYPE_NOSTRIFICATION;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_CON_CONFIRM) {
            $application->syncConConfirm();
            $docType = ProfileDoc::TYPE_CON_CONFIRM;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_EDUCATION_STATEMENT) {
            $docsChange = isset($profile->education_statement);
            $docType = ProfileDoc::TYPE_EDUCATION_STATEMENT;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_KT_CERTIFICATE) {
            $docsChange = isset($profile->kt_certificate);
            $docType = ProfileDoc::TYPE_KT_CERTIFICATE;
        }

        if ($inputs['doc_type'] == ProfileDoc::TYPE_WORK_BOOK) {
            $docsChange = isset($profile->doc_work_book);
            $docType = ProfileDoc::TYPE_WORK_BOOK;
        }
        
        // when we didn't find value from constans, use DB, in future better remove find by const
        if (!isset($docType)) {
            $type = ProfileDocsType::where('type', $inputs['doc_type'])->first();
            $docType = $type->type;
        }

        if (isset($docType)) {
            foreach ($request->file('files') as $file) {
                ProfileDoc::saveDocument($docType, $file, null, $singleFile);
            }
        }

        if ($docsChange){
            $profile->docs_status = Profiles::DOCS_STATUS_EDIT;
            $profile->save();
        }

        $application->save();

        return ['status' => 'success'];
        //return redirect()->back();
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function docsApplicationUploadPost(Request $request)
    {
        $type = $request->input('type');
        $date = $request->input('date');

        $docType = StudentRequestType::DOCS_TYPE_PREFIX . $type;

        $singleFile = true;
        if(count($request->file('files')) > 1) {
            $singleFile = false;
        }

        foreach ($request->file('files') as $file) {
            ProfileDoc::saveDocument($docType, $file, null, $singleFile);
        }
        $doc_id = ProfileDoc::where('user_id', Auth::user()->id)->max('id');

        $type_id = StudentRequestType::select('id')->where('key', $type)->first()->id;

        $studentRequest          = new StudentRequest;
        $studentRequest->user_id = Auth::user()->id;
        $studentRequest->type_id = $type_id;
        $studentRequest->doc_id  = $doc_id;
        $studentRequest->date    = $date;
        $studentRequest->save();


        /*
        if(!$file) {
            return Response::json([
                'status' => 'error',
                'message' => 'File param is empty'
            ]);
        }
        */

        //$userApplication = UserApplication::addApplication(Auth::user()->id, $file);
        //return ['status' => $userApplication ? 'success' : 'error'];
        return ['status' => 'success'];
    }

    
    public function getUserRequestsList()
    {
        return StudentRequest::getRequestsList();
    }

    public function getUserDocsList()
    {
        return ProfileDoc::getUserDocsList();
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finishRegistration()
    {
        return view('student.finish_registration');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editPhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faceimg' => 'required|image'
        ]);

        if($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $user = \App\Services\Auth::user();

        $faceImgName = $user->id . str_random(5) . '.jpg';
        $avatar = Avatar::make($request->file('faceimg'));
        $avatar->saveToFaces($faceImgName);

        $user->studentProfile->faceimg = $faceImgName;
        $user->studentProfile->save();

        return redirect()->back();
    }


    /**
     * generate education statement
     * @param
     * @return string
     * @throws \Exception
     */
    public function printEducationStatement()
    {
        return GenerateStudentContract::printEducationStatement(Auth::user()->id);
    }


    /**
     * generate education contract
     * @param
     * @return string
     * @throws \Exception
     */
    public function generateEducationContract()
    {
        return GenerateStudentContract::generateEducationContract(Auth::user()->id);
    }

}
