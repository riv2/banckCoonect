<?php
/**
 * User: dadicc
 * Date: 12/10/19
 * Time: 3:45 PM
 */

namespace App\Http\Controllers;

use Auth;
use App\{
    Bank,
    Nationality,
    ProfileDoc,
    Profiles,
    Role,
    User,
    UserBank,
    UserBusiness
};
use App\Services\{AgitatorHelper,RegistrationHelper,Service1C};
use App\Validators\{
    AgitatorRegisterControllerProflieIbanPostValidator,
    AgitatorRegisterControllerProfileIDPostValidator,
    AgitatorRegisterControllerProfileSaveImageValidator
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{File,Image,Log,Response,Session};

class AgitatorRegisterController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index( Request $request )
    {

        if( empty(Auth::user()) || empty(Auth::user()->studentProfile) )
        {
            abort(404);
        }

        if( Auth::user()->hasRole('agitator') && !Auth::user()->agitatorTestFinalRegistration()  )
        {
            return redirect()->route('home');
        }

        $oProfile = Auth::user()->studentProfile;
        if( $oProfile->isRedirectToRegisterAgitatorStep( Profiles::AGITATOR_REGISTRATION_STEP_USER_PROFILE_ID ) )
        {
            return $oProfile->getRegisterRouteAgitator( $oProfile->agitator_registration_step );
        }

        return view('agitator_registration.index');

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileID( Request $request )
    {

        $oUser = User::where('id', Auth::user()->id)->first();
        $oProfile = Auth::user()->studentProfile;
        if( empty($oProfile) || empty($oUser) )
        {
            abort(404);
        }

        if( !empty($oProfile->iin) )
        {

            // регистрируем юзера как агитатора
            Auth::user()->setRole( Role::NAME_AGITATOR );
            if( Auth::user()->hasClientRole() )
            {
                Auth::user()->unsetRole('guest');
            }
            $bResponse = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);
            if( empty($bResponse) )
            {
                abort(404);
            }

            // основные данные уже введены, редиректим на след шаг реги
            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_TERMS );

            return redirect()->route( Profiles::AGITATOR_REGISTRATION_STEP_TERMS );
        }

        return view('agitator_registration.profileID');

    }


    /**
     * @param Request $request
     */
    public function profileIDPost( Request $request )
    {

        // validation data
        $obValidator = AgitatorRegisterControllerProfileIDPostValidator::make( $request->all() );
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

        return redirect()->route('agitatorRegisterProfileEdit');

    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function profileEdit()
    {

        $oUser = User::where('id', Auth::user()->id)->first();
        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($oUser) || empty($profile) ) { abort(404); }

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
                return redirect()->route('agitatorRegisterProfileID')->withErrors([__("Error reading the front side of the ID, please try again")]);
            }

            if(!empty($SIDFront->img->photo)) {
                $SID['face'] = $SIDFront->img->photo;
            }

            if(!empty($SIDBack->str)) {
                foreach($SIDBack->str AS $key => $val) {
                    $SID[$key] = $val;
                }
            } else {
                return redirect()->route('agitatorRegisterProfileID')->withErrors([__("Error reading the back side of the ID, please try again")]);
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

                if( !empty($profilesCount) && ( $profilesCount->user_id != Auth::user()->id ) )
                {
                    return redirect()->route('agitatorRegisterProfileID')->withErrors([__("IIN already exists")]);
                }
            }

            if(isset($SID->surname)) $SID->fio = $SID->surname;
            if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
            if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;

            if(empty($SID->inn) || empty($SID->full_mrz) ||  !strpos($SID->full_mrz, $SID->inn) ) {
                return redirect()->route('agitatorRegisterProfileID')->withErrors([__("Mismatch between the back and front sides of the ID")]);
            }

            //checking expiration date
            if(empty($SID->expiry_date)) {
                return redirect()->route('agitatorRegisterProfileID')->withErrors([__("Can not read the expiration date")]);
            }
            if(strtotime($SID->expiry_date) <= time() ) {
                return redirect()->route('agitatorRegisterProfileID')->withErrors([__("Document is out of date")]);
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
                $oUser->name = $SID->name;
                $oUser->save();
            }

            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );

            // регистрируем юзера как агитатора
            Auth::user()->setRole( Role::NAME_AGITATOR );
            if( Auth::user()->hasClientRole() )
            {
                Auth::user()->unsetRole('guest');
            }
            $bResponse = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);
            if( empty($bResponse) )
            {
                abort(404);
            }

        } else {

            $profile->save();
            return redirect()->route('agitatorRegisterProfileIban');

        }

        return view('agitator_registration.profileEdit', compact('profile'));

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

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );

        $result['status'] = 'success';

        return json_encode($result);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileIdManual( Request $request )
    {

        $nationalityList = Nationality::get();
        $oUser = User::where('id', Auth::user()->id)->first();
        $profile = Auth::user()->studentProfile;
        if( empty($profile) || empty($oUser) )
        {
            abort(404);
        }

        if( !empty($profile->iin) )
        {

            // регистрируем юзера как агитатора
            Auth::user()->setRole( Role::NAME_AGITATOR );
            if( Auth::user()->hasClientRole() )
            {
                Auth::user()->unsetRole('guest');
            }
            $bResponse = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);
            if( empty($bResponse) )
            {
                abort(404);
            }

            // основные данные уже введены, редиректим на след шаг реги
            RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );

            return redirect()->route( Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );
        }

        return view('agitator_registration.profileIDManual', [
            'nationalityList' => $nationalityList,
            'profile' => $profile
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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

        $oUser = User::where('id', Auth::user()->id)->first();
        $profile = Profiles::where('user_id', Auth::user()->id)->first();
        if( empty($oUser) || empty($profile) ) { abort(404); }
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

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );

        if(isset($profile->fio)) {
            $aName = explode(' ', $profile->fio);
            if( !empty($aName[1]) )
            {
                $oUser->name = $aName[1];
                $oUser->save();
            }
        }

        $result['status'] = 'success';

        // регистрируем юзера как агитатора
        Auth::user()->setRole( Role::NAME_AGITATOR );
        if( Auth::user()->hasClientRole() )
        {
            Auth::user()->unsetRole('guest');
        }
        $bResponse = Service1C::registration($oUser->studentProfile->iin, $oUser->studentProfile->fio, $oUser->studentProfile->sex, $oUser->studentProfile->bdate);
        if( empty($bResponse) )
        {
            abort(404);
        }

        return json_encode($result);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileTerms( Request $request )
    {
        return view('agitator_registration.terms');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileIban( Request $request )
    {

        if( empty(Auth::user()->id) || empty(Auth::user()->studentProfile) )
        {
            abort(404);
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_INPUT_IBAN );

        return view('agitator_registration.profileIban',[
            'banks' => Bank::whereNUll('deleted_at')->get()
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileIbanPost( Request $request )
    {

        // validation data
        $obValidator = AgitatorRegisterControllerProflieIbanPostValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return Response::json([
                'status'  => false,
                'message' => __('Data not found')
            ]);
        }

        // сохраняем данные Юр лица
        if( $request->has('yr_data') && !empty($request->input('yr_data.name')) )
        {
            AgitatorHelper::saveUserBusiness( $request->input('yr_data') );
        }

        AgitatorHelper::saveUserBank( $request->input('bank_id'), $request->input('iban') );

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_FINISH );

        return Response::json([
            'status'  => true,
            'message' => __('Success')
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function profileFinish( Request $request )
    {
        return view('agitator_registration.finish');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileFinishPost( Request $request )
    {

        if( empty(Auth::user()->id) || empty(Auth::user()->studentProfile) )
        {
            abort(404);
        }

        RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_AGITATOR, Profiles::AGITATOR_REGISTRATION_STEP_FINISH );

        return redirect()->route('home');

    }


    /**
     * load profile photo
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profileLoadImage( Request $request )
    {

        if( !empty(Auth::user()->studentProfile) && !empty(Auth::user()->studentProfile->faceimg) )
        {
            return Response::json([
                'status'  => true,
                'message' => __('Success'),
                'image'   => Auth::user()->studentProfile->faceimg
            ]);
        }

        return Response::json([
            'status'  => false,
            'message' => __('Data not found')
        ]);

    }

}


