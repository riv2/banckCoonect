<?php
/**
 * User: dadicc
 * Date: 31.07.19
 * Time: 16:05
 */

namespace App\Http\Controllers\TeacherMiras;

use App\{
    AcademicDegree,
    City,
    Country,
    Language,
    LanguagesLevel,
    Nationality,
    Region,
    ProfileDoc,
    ScientificField,
    Speciality,
    TeachersEducation,
    TeachersExperience,
    User
};
use App\Http\Controllers\Controller;
use App\Services\Auth;
use App\Teacher\{ProfileTeacher};
use App\Validators\{
    TeacherMirasProfileAddAdressValidator,
    TeacherMirasProfileAddAdressInputsValidator,
    TeacherMirasProfileAddResumeValidator,
    TeacherMirasProfileEducationValidator,
    TeacherMirasProfileFamilyStatusValidator,
    TeacherMirasProfilePhoneCodeApprove,
    TeacherMirasProfilePhoneCodeValidator,
    TeacherMirasProfileSeniorityValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ProfileController extends Controller
{

    /**
     * страница подгрузки фото УЛ
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileID(Request $request)
    {

        $aInputs = $request->all();
        if( $request->has('back') && !empty($request->input('back')) )
        {
            return view('teacherMiras.profileID');
        }

        $oProfile = ProfileTeacher::where('user_id', Auth::user()->id)->first();
        if( !empty($oProfile->id) && empty($aInputs['error']) && ( ( !empty($oProfile->front_id_photo) && !empty($oProfile->back_id_photo) ) || !empty($oProfile->inn) ) )
        {
            // если есть данные переход к след шагу
            return redirect()->route(ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS );
        }

        return view('teacherMiras.profileID');
    }

    /**
     * обработчик страницы подгрузки фото УЛ
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function profileIDPost(Request $request)
    {

        $oProfile = ProfileTeacher::where('user_id', Auth::user()->id)->first();

        if( empty($oProfile->id) )
        {
            $oProfile = new ProfileTeacher;
            $oProfile->user_id = Auth::user()->id;

        } elseif ($oProfile->user_approved == 1 ) {

            // редирект на семейное положение
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS)->
            withErrors([__("You have already verified your profile")]);
        }

        // save
        ProfileDoc::saveDocument(ProfileDoc::TYPE_FRONT_ID, $request->file('front'));
        ProfileDoc::saveDocument(ProfileDoc::TYPE_BACK_ID, $request->file('back'));

        $oProfile->front_id_photo = '1';
        $oProfile->back_id_photo = '1';
        $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_PROFILE_EDIT;
        $oProfile->save();

        // редирект на обработку фото
        return redirect()->route(ProfileTeacher::REGISTRATION_STEP_PROFILE_EDIT);
    }

    /**
     * обработчик фото УЛ
     * @param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileEdit()
    {

        $oProfile = ProfileTeacher::where('user_id', Auth::user()->id)->first();

        $front = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_FRONT_ID)->first();
        $back = ProfileDoc::where('user_id', Auth::user()->id)->where('last', 1)->where('doc_type', ProfileDoc::TYPE_BACK_ID)->first();

        if ( !empty( $oProfile->getOriginal('front_id_photo') ))
        {
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

            if(!empty($SIDFront->str))
            {
                foreach($SIDFront->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {

                // редирект на страницу загрузки фото УЛ
                return redirect()->
                route(ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID, ['error' => 1])->
                withErrors([__("Error reading the front side of the ID, please try again")]);
            }

            if(!empty($SIDFront->img->photo))
            {
                $SID['face'] = $SIDFront->img->photo;
            }

            if(!empty($SIDBack->str)) {

                foreach($SIDBack->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {

                return redirect()->
                route(ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID, ['error' => 1])->
                withErrors([__("Error reading the back side of the ID, please try again")]);
            }

            $SID['init'] = 1;
            $SID = (object) $SID;

            if(isset($SID->surname)) $SID->fio = $SID->surname;
            if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
            if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;

            if(empty($SID->inn) || empty($SID->full_mrz) ||  !strpos($SID->full_mrz, $SID->inn) )
            {
                return redirect()->
                route(ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID, ['error' => 1])->
                withErrors([__("Mismatch between the back and front sides of the ID")]);
            }

            //checking expiration date
            if(empty($SID->expiry_date))
            {
                return redirect()->
                route(ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID, ['error' => 1])->
                withErrors([__("Can not read the expiration date")]);
            }
            if(strtotime($SID->expiry_date) <= time() )
            {
                return redirect()->
                route(ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID, ['error' => 1])->
                withErrors([__("Document is out of date")]);
            }

            if(isset($SID->face)) {
                $faceImgName = $SID->inn . str_random(5) . '.jpg';
                \File::put(public_path('images/uploads/faces/') . $faceImgName, base64_decode($SID->face));
                $oProfile->faceimg = $faceImgName;
            }

            $oProfile->user_id = Auth::user()->id;
            $oProfile->iin = $SID->inn;
            $oProfile->fio = $SID->fio;
            if(isset($SID->birth_date)) $oProfile->bdate = strtotime($SID->birth_date);
            if(isset($SID->number)) $oProfile->docnumber = $SID->number;
            if(isset($SID->issue_authority)) $oProfile->issuing = $SID->issue_authority;
            if(isset($SID->issue_date)) $oProfile->issuedate = strtotime($SID->issue_date);
            if(isset($SID->expiry_date)) $oProfile->expire_date = strtotime($SID->expiry_date);
            if( isset($inputs['nationality']) ) $oProfile->nationality = $SID->nationality;
            $oProfile->pass = 0;

            if($SID->gender_mrz == 'M') {
                $oProfile->sex = 1;
            } else {
                $oProfile->sex = 0;
            }
            $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS;
            $oProfile->save();

            if(isset($SID->name)) {
                $user = User::where('id', Auth::user()->id)->first();
                $user->name = $SID->name;
                $user->save();
            }

        }

        return view('pages.profileEdit', compact('profile'));
    }

    /**
     * ручная форма ввода профиля
     * @param
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileIDManual()
    {

        $profile = ProfileTeacher::where('user_id', Auth::user()->id)->first();

        if( !empty($profile->id) && !empty($profile->front_id_photo) && ( strlen($profile->front_id_photo) > 3 ) )
        {
            // если фото УЛ загружено переход к след шагу
            $profile->registration_step = ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS;
            $profile->save();

            return redirect()->route(ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS);
        }

        $nationalityList = Nationality::get();
        $oCountryList = Country::get();

        return view('teacherMiras.profileIDManual', compact('profile', 'nationalityList', 'oCountryList'));
    }

    /**
     * обработчик ручной формы ввода профиля
     * @param Request $request
     * @return string
     */
    public function profileIDManualPost(Request $request)
    {

        $inputs = $request->all();
        $alien = ( !empty($inputs['alien']) && ( $inputs['alien'] == 'true' ) ) ? ProfileTeacher::ALIEN_STATUS_ALIEN : ProfileTeacher::ALIEN_STATUS_RESIDENT;

        if(strtotime($request['expire_date']) <= time() && !$alien) {

            $result['status'] = 'fail';
            $result['text'] = __("Document is out of date");
            return json_encode($result);
        }

        $oProfile = ProfileTeacher::where('user_id', Auth::user()->id)->first();
        if( !empty($oProfile) && $oProfile->registration_step == ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS )
        {
            $result['status'] = 'success';
            return json_encode($result);
        }

        if( empty($oProfile->id) ){

            $oProfile = new ProfileTeacher;
            $oProfile->user_id = Auth::user()->id;
        }

        $oProfile->front_id_photo = '0';
        $oProfile->back_id_photo = '0';

        $oProfile->user_id = Auth::user()->id;
        if(isset($inputs['iin'])) $oProfile->iin = $inputs['iin'];
        if(isset($inputs['fio'])) $oProfile->fio = $inputs['fio'];
        if(isset($inputs['bdate'])) $oProfile->bdate = $inputs['bdate'];
        if(isset($inputs['docnumber'])) $oProfile->docnumber = $inputs['docnumber'];
        if(isset($inputs['issuing'])) $oProfile->issuing = $inputs['issuing'];
        if(isset($inputs['issuedate'])) $oProfile->issuedate = $inputs['issuedate'];
        if(isset($inputs['expire_date'])) $oProfile->expire_date = $inputs['expire_date'];
        if(isset($inputs['nationality']) ) $oProfile->nationality_id = $inputs['nationality'];
        if(isset($inputs['citizenship']) ) $oProfile->citizenship_id = $inputs['citizenship'];
        if(isset($inputs['citizenship']) ) $oProfile->citizenship_id = $inputs['citizenship'];
        $oProfile->alien = $alien;
        $oProfile->pass = 0;
        if(isset($inputs['sex']) ) $oProfile->sex = $inputs['sex'];

        $oProfile->user_approved = 1;
        $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS;
        $oProfile->save();

        if(isset($oProfile->fio)) {
            $aName = explode(' ', $oProfile->fio);
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
     * страница ввода семейного положения
     * @param Request $request
     */
    public function profileFamilyStatus(Request $request)
    {

        return view('teacherMiras.profileFamilyStatus');
    }

    /**
     * обработчик ввода семейного положения
     * @param Request $request
     */
    public function profileFamilyStatusPost(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfileFamilyStatusValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS)->
            withErrors([__('Family input error')]);
        }

        $profile = ProfileTeacher::
        where('user_id',Auth::user()->id)->
        first();

        $profile->fill( $request->all() );
        $profile->registration_step = ProfileTeacher::REGISTRATION_STEP_ADD_ADRESS;
        $profile->save();

        // переход к шагу 3 - ввода адреса
        return redirect()->
        route(ProfileTeacher::REGISTRATION_STEP_ADD_ADRESS);
    }

    /**
     * страница загрузки адресной справки
     * @param Request $request
     */
    public function profileAdress(Request $request)
    {

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( !empty( $oProfile->actual_address ) || !empty( $oProfile->home_address ) )
        {
            // если есть адресс то переход на страницу ввода резюме
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME);
        }

        $oCountry = Country::orderBy('name','ASC')->get();
        $oRegion = Region::orderBy('name','ASC')->get();
        $oCity = City::orderBy('name','ASC')->get();

        $sCurrentLocale = app()->getLocale();
        $locale = Language::getFieldName('name', $sCurrentLocale, Language::LANGUAGE_EN, Language::LANGUAGE_RU);

        return view('teacherMiras.profileAdress',[
            'country' => $oCountry,
            'region'  => $oRegion,
            'city'    => $oCity,
            'locale'  => $locale,
            'profile' => $oProfile
        ]);
    }

    /**
     *
     * обработчик ввода адреса из справки
     * @param Request $request
     */
    public function profileAdressPost(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfileAddAdressInputsValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_ADD_ADRESS)->
            withErrors([__('Address input error')]);
        }

        if( $request->hasFile('address_card') )
        {
            // save
            ProfileDoc::saveDocument(ProfileDoc::TYPE_TEACHER_MIRAS_ADDRESS_CARD, $request->file('address_card'));
        }

        $oProfile = ProfileTeacher::
        where('user_id',Auth::user()->id)->
        first();

        $oProfile->fill( $request->all() );
        $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_ENTER_MOBILE_PHONE;
        $oProfile->save();

        // переход к шагу 4 - ввода телефона
        return redirect()->
        route(ProfileTeacher::REGISTRATION_STEP_ENTER_MOBILE_PHONE);
    }

    /**
     * страница ввода телефона
     * @param Request $request
     */
    public function profilePhone(Request $request)
    {

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( !empty( $oProfile->mobile ) || !empty( $oProfile->home_phone ) )
        {
            // если есть телефон то переход на страницу ввода резюме
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME);
        }
        return view('teacherMiras.profilePhone');
    }

    /**
     * @param Request $request
     * @return string
     */
    public function profileMobileSendCode(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfilePhoneCodeValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return Response::json([
                'status' => 'fail',
                'text'   => __('Phone input error')
            ]);
        }

        if( $request->has('home_phone') )
        {
            $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
            $oProfile->home_phone = $request->input('home_phone');
            $oProfile->save();
        }

        \App\Services\Auth::user()->sendPhoneConfirmCode($request->input('mobile'));
        $result['status'] = 'success';

        return json_encode($result);
    }

    /**
     * approve phone
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileMobileApprove(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfilePhoneCodeApprove::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return Response::json([
                'status' => 'fail',
                'text'   => __('Confirmation code error')
            ]);
        }

        $phoneConfirm = \App\Services\Auth::user()->checkPhoneConfirmCode($request->input('code'));
        if(!$phoneConfirm)
        {
            return Response::json([
                'status' => 'fail',
                'text'   => __('Confirmation code error')
            ]);
        }

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        $oProfile->mobile = $phoneConfirm->phone_number;
        $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME;
        $oProfile->save();

        // переход к шагу 5 - ввода резюме
        return Response::json([
            'redirect' => route( ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME )
        ]);
    }

    /**
     * страница ввода резюме
     * @param Request $request
     */
    public function profileResume(Request $request)
    {

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( !empty( $oProfile->resume_link ) && !empty( $oProfile->teacher_miras_resume  ) )
        {
            // если есть телефон то переход на страницу ввода резюме
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION);
        }
        return view('teacherMiras.profileResume');
    }

    /**
     * страница обработки ввода резюме
     * @param Request $request
     */
    public function profileResumePost(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfileAddResumeValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME)->
            withErrors([__('Error adding resume')]);
        }

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( $request->hasFile('resume_file') )
        {
            // save
            ProfileDoc::saveDocument(ProfileDoc::TYPE_TEACHER_MIRAS_RESUME, $request->file('resume_file'),false);
        }

        if( $request->has('resume_link') )
        {
            $oProfile->resume_link = $request->input('resume_link');
        }
        $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION;
        $oProfile->save();

        // переход к вводу образования
        return redirect()->
        route(ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION);
    }

    /**
     * страница ввода образования
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileEducation(Request $request)
    {

        //$oProfile = ProfileTeacher::where('user_id', '=', 4822)->first();
        /*
        if( !empty( $oProfile->resume_link ) && !empty( $oProfile->teacher_miras_resume  ) )
        {
            // если есть телефон то переход на страницу ввода резюме
            return Response::json([
                'redirect' => ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION
            ]);
        }
        */

        // List
        $oLanguage = Language::orderBy('sorting','DESC')->get();
        $oLanguagesLevel = LanguagesLevel::orderBy('id','ASC')->get();
        $oAcademicDegree = AcademicDegree::orderBy('name','ASC')->get();
        $oScientificField = ScientificField::orderBy('name','ASC')->get();
        $oSpeciality = Speciality::where('year',date('Y'))->orderBy('name','ASC')->get();

        $sCurrentLocale = app()->getLocale();
        $locale = Language::getFieldName('name', $sCurrentLocale);

        return view('teacherMiras.profileEducation',[
            'language'        => $oLanguage,
            'languagesLevel'  => $oLanguagesLevel,
            'academicDegree'  => $oAcademicDegree,
            'scientificField' => $oScientificField,
            'speciality'      => $oSpeciality,
            'locale'          => $locale
        ]);
    }

    /**
     * обработка образования
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileEducationPost(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfileEducationValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_SENIORITY)->
            withErrors([ __('Error adding seniority') ]);
        }

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($oProfile) )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_SENIORITY)->
            withErrors([__('Error login teacher')]);
        }

        if( $request->has('education') )
        {
            $aData = $request->all();
            $iFormCount = 1;
            if( !empty( $aData['education']['type'] ) ) {
                $iFormCount = count($aData['education']['type']);
            }
            for($i=0;$i<$iFormCount;$i++)
            {
                $oTeachersEducation = new TeachersEducation();

                if( !empty( $aData['education']['type'][$i] ) ){
                    $oTeachersEducation->type = $aData['education']['type'][$i];
                }
                if( !empty( $aData['education']['date_from'][$i] ) ){
                    $oTeachersEducation->date_from = $aData['education']['date_from'][$i];
                }
                if( !empty( $aData['education']['date_to'][$i] ) ){
                    $oTeachersEducation->date_to = $aData['education']['date_to'][$i];
                }
                if( !empty( $aData['education']['education_place'][$i] ) ){
                    $oTeachersEducation->education_place = $aData['education']['education_place'][$i];
                }
                if( !empty( $aData['education']['qualification_awarded'][$i] ) ){
                    $oTeachersEducation->qualification_awarded = $aData['education']['qualification_awarded'][$i];
                }
                if( !empty( $aData['education']['speciality'][$i] ) ){
                    $oTeachersEducation->speciality = $aData['education']['speciality'][$i];
                }
                if( !empty( $aData['education']['nostrification'][$i] ) ){
                    $oTeachersEducation->nostrification = $aData['education']['nostrification'][$i];
                }
                if( !empty( $aData['education']['academic_degree_id'][$i] ) ){
                    $oTeachersEducation->academic_degree_id = $aData['education']['academic_degree_id'][$i];
                }
                if( !empty( $aData['education']['scientific_field_id'][$i] ) ){
                    $oTeachersEducation->scientific_field_id = $aData['education']['scientific_field_id'][$i];
                }
                if( !empty( $aData['education']['dissertation_topic_1'][$i] ) ){
                    $oTeachersEducation->dissertation_topic_1 = $aData['education']['dissertation_topic_1'][$i];
                }
                if( !empty( $aData['education']['dissertation_topic_2'][$i] ) ){
                    $oTeachersEducation->dissertation_topic_2 = $aData['education']['dissertation_topic_2'][$i];
                }
                if( !empty( $aData['education']['protocol_number'][$i] ) ){
                    $oTeachersEducation->protocol_number = $aData['education']['protocol_number'][$i];
                }
                if( !empty( $aData['education']['academic_title'][$i] ) ){
                    $oTeachersEducation->academic_title = $aData['education']['academic_title'][$i];
                }
                if( !empty( $aData['education']['protocol_date'][$i] ) ){
                    $oTeachersEducation->protocol_date = $aData['education']['protocol_date'][$i];
                }
                if( !empty( $aData['education']['embership_academies'][$i] ) ){
                    $oTeachersEducation->embership_academies = $aData['education']['embership_academies'][$i];
                }
                if( !empty( $aData['education']['lang_id'][$i] ) ){
                    $oTeachersEducation->lang_id = $aData['education']['lang_id'][$i];
                }
                if( !empty( $aData['education']['lang_level_id'][$i] ) ){
                    $oTeachersEducation->lang_level_id = $aData['education']['lang_level_id'][$i];
                }
                if( !empty( $aData['education']['data_input'][$i] ) ){
                    $oTeachersEducation->data_input = $aData['education']['data_input'][$i];
                }

                // FILES
                if( !empty($aData['education']['diploma_photo'][$i]) ) {
                    ProfileDoc::saveDocument(ProfileDoc::TYPE_DIPLOMA, $aData['education']['diploma_photo'][$i]);
                }
                if( !empty($aData['education']['atteducation'][$i]) ) {
                    ProfileDoc::saveDocument(ProfileDoc::TYPE_ATTEDUCATION, $aData['education']['atteducation'][$i]);
                }
                if( !empty($aData['education']['atteducation_back'][$i]) ) {
                    ProfileDoc::saveDocument(ProfileDoc::TYPE_ATTEDUCATION_BACK, $aData['education']['atteducation_back'][$i]);
                }
                if( !empty($aData['education']['certificate_file'][$i]) ) {
                    ProfileDoc::saveDocument(ProfileDoc::TYPE_TEACHER_MIRAS_CERTIFICATE, $aData['education']['certificate_file'][$i]);
                }
                if( !empty($aData['education']['certificate_lang_file'][$i]) ) {
                    ProfileDoc::saveDocument(ProfileDoc::TYPE_TEACHER_MIRAS_LaNG_CERTIFICATE, $aData['education']['certificate_lang_file'][$i]);
                }


                $oTeachersEducation->user_id = \App\Services\Auth::user()->id;
                $oTeachersEducation->save();
                unset($oTeachersEducation);
            }
            $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_SENIORITY;
            $oProfile->save();
        }

        // переход к вводу трудового стажа
        return redirect()->
        route(ProfileTeacher::REGISTRATION_STEP_SENIORITY);
    }

    /**
     * страница ввода трудового стажа
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileSeniority(Request $request)
    {

        //$oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        /*
        if( !empty( $oProfile->resume_link ) && !empty( $oProfile->teacher_miras_resume  ) )
        {
            // если есть телефон то переход на страницу ввода резюме
            return Response::json([
                'redirect' => ProfileTeacher::REGISTRATION_STEP_ENTER_EDUCATION
            ]);
        }
        */
        return view('teacherMiras.profileSeniority');
    }

    /**
     * обработка трудового стажа
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileSeniorityPost(Request $request)
    {

        // validation data
        $obValidator = TeacherMirasProfileSeniorityValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_SENIORITY)->
            withErrors( $obValidator->errors() ); // __('Error adding seniority')
        }

        $oProfile = ProfileTeacher::where('user_id', '=', \App\Services\Auth::user()->id)->first();
        if( empty($oProfile) )
        {
            // возврат на страницу ввода с ошибкой
            return redirect()->
            route(ProfileTeacher::REGISTRATION_STEP_SENIORITY)->
            withErrors([__('Error login teacher')]);
        }

        if( $request->has('experience') )
        {
            $aData = $request->all();
            $iFormCount = count($aData['experience']['date_from']);
            for($i=0;$i<$iFormCount;$i++)
            {
                $oTeachersExperience = new TeachersExperience();
                $oTeachersExperience->date_from = $aData['experience']['date_from'][$i] ?? '';
                $oTeachersExperience->date_to   = $aData['experience']['date_to'][$i] ?? '';
                $oTeachersExperience->workplace   = $aData['experience']['workplace'][$i] ?? '';
                $oTeachersExperience->type_experience   = $aData['experience']['type_experience'][$i] ?? '';
                $oTeachersExperience->current_experience   = $aData['experience']['current_experience'][$i] ?? '';
                $oTeachersExperience->workstatus   = $aData['experience']['workstatus'][$i] ?? '';
                $oTeachersExperience->charges   = $aData['experience']['charges'][$i] ?? '';
                $oTeachersExperience->contacts   = $aData['experience']['contacts'][$i] ?? '';
                $oTeachersExperience->user_id = \App\Services\Auth::user()->id;
                $oTeachersExperience->save();
                unset($oTeachersExperience);
            }
            $oProfile->registration_step = ProfileTeacher::REGISTRATION_STEP_FINISH;
            $oProfile->save();
        }

        return view('teacherMiras.profileConfirmRegistration');
    }

}