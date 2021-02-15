<?php

namespace App\Http\Controllers;

use App\ActivityLog;
use App\Services\Auth;
use App;
use App\User;
use App\Settings;
use App\Service;
use App\ServiceTranslation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Redirect;
use Mail;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\BcApplications;
use App\MgApplications;
use App\Profiles;
use App\Services\{KeyCloak,RegistrationHelper};
use App\Validators\ErrorReportFormValidator;
use Ixudra\Curl\Facades\Curl;
use App\FinanceNomenclature;
use App\Language;
use App\StudentFinanceNomenclature;
use App\Services\Domain;

use Illuminate\Routing\UrlGenerator;

class IndexController extends Controller
{


    public function index(Request $request)
    {
        if (!$this->alreadyInstalled()) {
            // return redirect('install');
        }

        if( \Session::has('flash_message') )
        {
            $message = \Session::get('flash_message');
            \Session::flush();
            \Session::flash('flash_message', $message);
        }

        $subdomain = Domain::getSubdomain();

        if(!Auth::check())
        {
            if($subdomain == 't')
            {
                return redirect()->route('teacherLogin');
            }
            return redirect()->route('studentLogin');
        }
        else
        {
            if($subdomain == 't')
            {
                return redirect()->route('teacherProfile');
            }

            ActivityLog::userVisitedPage('Главная');

            return view('pages.dashboard');
        }

        //return view('pages.index');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function agitator()
    {
        return view('pages.agitator');
    }

    public function agitatorPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'  => 'required',
            'phone' => 'required'
        ]);

        if($validator->fails())
        {
            return \redirect()->back()->withErrors($validator->messages());
        }

        $phone = $request->input('phone');
        $phone = strpos($phone, '+') === 0 ? $phone : '+7' . $phone;

        if( User::where('phone', $phone)->where('agitator', true)->count() )
        {
            return \redirect()->back()->withErrors( __('Agitator already registered') );
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->email = '';
        $user->phone = strpos($phone, '+') === 0 ? $phone : '+7' . $phone;
        $user->password = '';
        $user->status = 1;
        $user->keycloak = false;
        $user->agitator = true;
        $user->save();

        \Session::flash('flash_message', __('Agitator successfully registered.'));
        return \redirect()->back();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agitatorAjaxList(Request $request)
    {
        $text = $request->input('text');

        $usersList = User
            ::select('id', 'name', 'phone')
            ->where('agitator', true)
            ->where(function($query) use($text){
                $query->whereRaw("name LIKE '" . $text . "%'");
                $query->orWhereRaw("phone LIKE '" . $text . "%'");
            })
            ->limit(10)
            ->get();

        return Response::json($usersList);
    }

    /**
     * If application is already installed.
     *
     * @return bool
     */
    public function alreadyInstalled()
    {
        return file_exists(storage_path('installed'));
    }


    public function aboutme_page()
    {
        $services = Service::get();

        $locale = App::getLocale();

        forEach ($services AS $service) {
            $servicesTranslation = ServiceTranslation
                ::where('service_id', '=', $service->id)
                ->where('locale', '=', $locale)
                ->first();

            if (!empty($servicesTranslation->id)) {
                $service->name = $servicesTranslation->name;
                $service->description = $servicesTranslation->description;
            }
        }

        $settings = Settings::findOrFail('1');

        if ($locale == 'ru') {
            $settings->about_us_title = $settings->about_us_title_ru;
            $settings->about_us_description = $settings->about_us_description_ru;
        }

        return view('pages.about-me', compact('settings', 'services'));
    }

    public function terms_conditions_page()
    {
        return view('pages.terms_conditions');
    }

    public function privacy_policy_page()
    {
        return view('pages.privacy');
    }

    public function testimonials()
    {
        return view('pages.testimonials');
    }

    public function callBackForm()
    {
        if (Auth::check()){
            $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();
        }else{
            $profile = null;
        }
        return view('pages.callBack' , compact('profile'));
    }

    public function callBack(Request $request)
    {

        $data = \Input::except(array('_token'));
        $inputs = $request->all();

        $rule = ['number' => 'required'];

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        Mail::send('emails.callBack',
            [ 'number' => $inputs['number'] ], function ($message) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( explode(',',env('MAIL_FOR_ERROR_REPORT_FORM')) )->subject('Заявка на обратный звонок');
            });


        return redirect('/deansoffice/#callback')->with('flash_message_callback', __("Thank you for the application"));
    }


    /**
     * Do user login
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        if (!Auth::check()) {
            return view('auth.login_old');
        }

        $profile = Profiles::where('user_id', '=', Auth::user()->id)->first();

        if(!$profile)
        {
            return redirect()->route('userProfileID');
        }

        if($profile->status == 'moderation') { 

            $bcApplication = BcApplications::where('user_id', \App\Services\Auth::user()->id)->first();
            $mgApplication = MgApplications::where('user_id', \App\Services\Auth::user()->id)->first();

            if($bcApplication || $mgApplication) {

                RegistrationHelper::setRegistrationStep( Profiles::REGISTRATION_TYPE_CLIENT, Profiles::REGISTRATION_STEP_AFTER_ID );
                return redirect()->route('afterID');
            }

            return redirect()->route('userProfileID');
        }

        //return redirect()->route('userProfile');
        return redirect()->route('financesPanel');
        
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        /*Standart user auth*/
        if (Auth::attempt($credentials, $request->has('remember'))) {

            if (Auth::user()->status == '0') {
                \Auth::logout();
                return redirect()
                    ->back()
                    ->with(['resendUrl' => route('resendCodeToEmail', ['email' => $request->input('email')])])
                    ->withErrors(__('Your account is still activated, please check your mailbox') . '.');
            }

            if (!(Auth::user()->hasRole(App\Role::NAME_TEACHER ) || Auth::user()->hasRole(App\Role::NAME_CLIENT ))) {
                \Auth::logout();
                return redirect()->back()->withErrors(__("A couple of email and password entered incorrectly. try again"));
            }

            if (User::getCurrentRole() == App\Role::NAME_TEACHER &&
                isset(Auth::user()->teacherProfile) &&
                Auth::user()->teacherProfile->status == 'block')
            {
                \Auth::logout();
                return redirect()->back()->withErrors(__('Your account has been blocked'));
            }

            ActivityLog::teacherLogin();

            $this->getBallance();

            return redirect()->route('userProfile');
        }
        else
        {
            /*Auth with keycloak*/
            $user = User
                ::where('email', $request->input('email'))
                ->where('keycloak', true)
                ->first();

            if($user)
            {
                $keyCloak = new KeyCloak([
                    'realm' => env('KEYCLOAK_REALM'),
                    'auth-server-url' => env('KEYCLOAK_SERVER_URL'),
                    'resource' => env('KEYCLOAK_CLIENT_ID'),
                    'realm-public-key' => env('KEYCLOAK_REALM_PUBLIC_KEY'),
                    'client-secret' => env('KEYCLOAK_CLIENT_SECRET'),
                    'secret' => env('KEYCLOAK_CLIENT_SECRET')
                ]);

                if ($keyCloak->grant_from_login($request->input('email'), $request->input('password'))) {
                    if(Auth::login($user, $request->has('remember')))
                    {
                        $this->getBallance();
                        return redirect()->route('userProfile');
                    }
                }
            }
        }

        return redirect()->back()->withErrors(__("A couple of email and password entered incorrectly. try again"));
    }

    public function sidtest()
    {
        return view('pages.sidtest');
    }

    public function sidtestPost(Request $request)
    {
        $inputs = $request->all();
        $path = public_path('images/uploads/');

        if($inputs['type'] == 'auto') {
            $type = 'kaz.id.*';
        } elseif($inputs['type'] == 'type1') {
            $type = 'kaz.id.type1';
        } elseif($inputs['type'] == 'type2') {
            $type = 'kaz.id.type2';
        }

        if( $request->has('front') ) {
            $fileName =  strtolower(str_random(5)) . '-' . time() . '.jpg' ;
            $request->file('front')->move($path, $fileName);

            
            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.$path . $fileName.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'';
            $SID = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SID);
            $SIDFront = json_decode($SID);
            unlink($path . $fileName);
        }

        if( $request->has('back') ) {
            $fileName =  strtolower(str_random(5)) . '-' . time() . '.jpg' ;
            $request->file('back')->move($path, $fileName);

            $shell = 'php7.2 '.__DIR__.'/SmartID/SmartID.php '.$path . $fileName.' '.__DIR__.'/SmartID/bundle_kaz_mrz_server.zip '.$type.'';
            $SID = shell_exec($shell);
            Log::info('— command: '.$shell.' return: '.$SID);
            $SIDBack = json_decode($SID);
            unlink($path . $fileName);
        }
        $SID = array();
        if(!empty($SIDFront->str)) {
            foreach($SIDFront->str AS $key => $val) {
                $SID[$key] = $val;
            }
        } else {
            print( __("Error reading the front side of the ID, please try again")."<br>" );
        }
        
        if(!empty($SIDFront->img->photo)) {
            $SID['face'] = $SIDFront->img->photo;
        }

        if(!empty($SIDBack->str)) {
            foreach($SIDBack->str AS $key => $val) {
                $SID[$key] = $val;
            }
        } else {
            print( __("Error reading the back side of the ID, please try again")."<br>" );
        }

        $SID['init'] = 1;
        $SID = (object) $SID;
        //print_r($SID);

        if(isset($SID->surname)) $SID->fio = $SID->surname;
        if(isset($SID->name)) $SID->fio .= ' '.$SID->name;
        if(isset($SID->patronymic)) $SID->fio .= ' '.$SID->patronymic;
        
        if(empty($SID->inn) || empty($SID->full_mrz) ||  !strpos($SID->full_mrz, $SID->inn) ) {
            print( __("Mismatch between the back and front sides of the ID") );
        }

        //checking expiration date
        if(isset($SID->expiry_date) && strtotime($SID->expiry_date) <= time() ) {
            print(__("Document is out of date"));
        }



        dd($SID);


        return view('pages.sidtest');
    }   
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function register(Request $request)
    {

        $aInputs = $request->all();

        if ( Auth::check() && empty($aInputs['receipt']) ) {

            return redirect()->route('home');
        }

        $receipt = false;
        if( $request->has('receipt') )
        {
            $receipt = true;
        }
        $register_fio = '';
        if( $request->has('register_fio') )
        {
            $register_fio = $request->input('register_fio');
        }

        return view('auth.register',[
            'receipt'       => $receipt,
            'register_fio'  => $register_fio
        ]);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function postRegister(Request $request)
    {

        $data = \Input::except(['_token']);

        $inputs = $request->all();

        $rule = [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];

        $validator = \Validator::make($data, $rule);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->messages());
        }

        //$maxGroupId = User::max('group_id')+1;

        $user = new User;

        //$user->salon_id = $salonID;
        $string = str_random(15);
        $user_name = '';
        $user_email = $inputs['email'];

        $user->status = '0';
        $user->name = '';
        $user->email = $user_email;
        $user->password = bcrypt($inputs['password']);

        if (!empty($inputs['register_fio'])) {
            $user->register_fio = $inputs['register_fio'];
        }

        //$user->phone= $inputs['phone'];
        //$user->group_id= $maxGroupId;

        $user->confirmation_code = $string;

        $user->save();
        $user->setRole(User::getCurrentRole());

        User::updateSimpleSearchCache($user);

        Mail::send('emails.register_confirm',
            [
                'email' => $inputs['email'],
                'password' => $inputs['password'],
                'confirmation_code' => $string,
                'user_message' => 'test'
            ], function ($message) use ($user_name, $user_email) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to($user_email, $user_name)->subject(__('Registration Confirmation'));
            });

        if (empty($inputs['receipt'])) {
            $inforEmail = explode(';', env('NEW_REGISTRATION_REPORT_EMAiL'));
            Mail::send('emails.new_registration',
                [
                    'email' => $inputs['email']
                ], function ($message) use ($inforEmail) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to($inforEmail)->subject(__('New registration'));
                });
        }

        \Session::flash('flash_message', __('Please confirm your registration. We sent you a confirmation link to your inbox'));

        return redirect()->route('home');
    }

    /**
     * @param $email
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendCodeToEmail($email)
    {
        $user = User::where('email', $email)->where('status', false)->first();

        if (!$user)
        {
            return redirect()->route('login')->withErrors([__("You have already confirmed your e-mail")]);
        }

        $user_name = $user->name;
        $user_email = $user->email;

        if(!$user->confirmation_code)
        {
            $user->confirmation_code = str_random(15);
            $user->save();
        }

        Mail::send('emails.register_confirm',
            array(
                'email' => $user->email,
                'confirmation_code' => $user->confirmation_code,
                'user_message' => 'test'
            ), function ($message) use ($user_name, $user_email) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to($user_email, $user_name)->subject('Registration Confirmation');
            });

        \Session::flash('flash_message', __('Please confirm your registration. We have sent you a confirmation link to your inbox'));

        return redirect()->route('home');
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {

        if ( !empty(Auth::user()->studentProfile) && (Auth::user()->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE) ) {
            $activityUserLog = ActivityLog::where('user_id', Auth::user()->id)->where('log_type', ActivityLog::AUTH_ONLINE_LOG)->orderByDesc('id')->first();

            if (isset($activityUserLog)){
                $properties = $activityUserLog->properties;
                $properties['to'] = Carbon::now()->format('d-m-Y H:i:s');
                $activityUserLog->properties = $properties;
                $activityUserLog->save();
            }
        }

        ActivityLog::logout();
        
        Auth::logout();

        return redirect()->route('home');
    }

    /**
     * @param $code
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function confirm($code)
    {

        $user = User::where('confirmation_code', $code)->where('status', 0)->first();

        if(!$user)
        {
            return redirect()->route('home');
        }

        $user->status = '1';
        $user->save();

        if(isset($user)) {
            Auth::login($user);
        }

        if( !empty($user) && $user->hasTeacherMirasRole() )
        {
            return view('teacherMiras.confirm');
        }

        return view('auth.confirm');

        //\Session::flash('flash_message', __('Verification successful'));

        //return view('auth.login');
    }

    public function imageUpload($image, $fileExt, $tmpFilePath)
    {
        $fileName =  str_slug($image->getClientOriginalName(), '-').'-'.md5(str_random(7));
        $fileExtName = $fileName.$fileExt;
        $image->move(public_path($tmpFilePath), $fileName);
        if( \File::mimeType( public_path($tmpFilePath.$fileName) ) == 'application/octet-stream' ) {
            shell_exec('tifig  -v -p '.$image->getPathName().' ' .public_path($tmpFilePath).$fileExtName);
        } else {
            rename(public_path($tmpFilePath).$fileName, public_path($tmpFilePath).$fileExtName);
        }
        return $fileExtName;
    }

    public function report()
    {
        $user_id = '';
        $profile = false;
        if( Auth::user() )
        {
            $user_id = Auth::user()->id;
            $profile = Auth::user()->studentProfile;
        }
        return view('pages.errorReport',[
            'profile' => $profile,
            'user_id' => $user_id
        ]);
    }

    public function reportPost(Request $request)
    {
        $data = \Input::except(array('_token'));

        $aReasonList = [
            "Cancellation of purchase discipline",  // отмена покупки дисциплины
        ];

        $validator = ErrorReportFormValidator::make($request->all());
        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator->messages());
        }

        $inputs = $request->all();

        $reason = null;
        $aEmails = explode(',',env('MAIL_FOR_ERROR_REPORT_FORM'));
        if( $request->has('specified_reason') && in_array($request->input('specified_reason'),$aReasonList) )
        {
            $reason = __($request->input('specified_reason'));
            $aEmails[] = env('MAIL_FOR_ERROR_REPORT_FORM_OFFICE');
        }

        Mail::send('emails.error_report',
            array(
                'user_id'   => !empty($inputs['user_id']) ? $inputs['user_id'] : '',
                'fio'       => $inputs['fio'],
                'phone'     => $inputs['phone'],
                'text'      => $inputs['message'],
                'reason'    => $reason
            ), function ($message) use ($aEmails) {
                $message->from(getcong('site_email'), getcong('site_name'));
                $message->to( $aEmails )->subject('error reporting form');
            });

        \Session::flash('flash_message', __('Thanks for the information, we will check the problem you described'));

        $user_id = '';
        $profile = false;
        if( Auth::user() )
        {
            $user_id = Auth::user()->id;
            $profile = Auth::user()->studentProfile;
        }

        return view('pages.errorReport',[
            'profile' => $profile,
            'user_id' => $user_id
        ]);
    }

    /**
     * @param $url
     * @param string $type
     * @param array $params
     * @return string
     */
    static public function getBallance()
    {
        $profile = Profiles::select('iin')->where('user_id', Auth::user()->id)->first();

        if(!($profile && $profile->iin))
        {
            return 0;
        }

        $user = User::where('id', Auth::user()->id)->first();

        /*$iin = $profile->iin;
        $login = 'kit';
        $password = '1q2w3e4r';
        $server = ( config('app.debug') == true )?'185.22.66.224':'10.0.6.217';
        $url = 'http://' . $login . ':' . $password . '@' . $server . '/site/hs/Balance/' . $iin;

        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        
        $response = Curl::to($url)
            ->withHeader('User-Agent: ' . $userAgent)
            ->withResponseHeaders()
            ->returnResponseObject()
            ->allowRedirect();

            $user->balance = $response->get()->content;
            $user->save();*/

        return $user->balance ?? 0;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function registrationBlank(Request $request)
    {
        return view('pages.registrationBlank');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fitnessRoom()
    {
        return view('pages.fitness_room');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cafeteria()
    {
        return view('pages.cafe');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pool()
    {
        return view('pages.pool');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function training()
    {
        return view('pages.training');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bus()
    {
        return view('pages.bus');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qr()
    {
        return view('pages.qr');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gid()
    {
        return view('pages.gid');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function helps()
    {

        $user = Auth::user()->id;
        $sCurrentLocale = app()->getLocale();
        $locale = Language::getFieldName('name', $sCurrentLocale);
        $profile = Auth::user()->studentProfile;

        $FinanceNomenclature = FinanceNomenclature::
        where('code','00000003274')->
        whereNull('deleted_at')->
        get();
        $boughtServiceIds = StudentFinanceNomenclature::getBoughtServiceIds(Auth::user()->id, (!empty(Auth::user()->studentProfile) && !empty(Auth::user()->studentProfile->currentSemester())) ? Auth::user()->studentProfile->currentSemester() : 1 );

        // get unique rows
        $aUniaueName = [];
        $aFinanceNomenclature = [];
        if( !empty($profile) && !empty($FinanceNomenclature) && (count($FinanceNomenclature) > 0) )
        {
            foreach( $FinanceNomenclature as $one )
            {
                if( in_array($one->name,$aUniaueName) === false )
                {

                    if( $profile->education_study_form == Profiles::EDUCATION_STUDY_FORM_FULLTIME )
                    {
                        if( $one->name_en == 'Reference to the military commissariat' )
                        {

                            if( $profile->sex == Profiles::GENDER_MALE )
                            {

                                $aUniaueName[] = $one->name;
                                $aFinanceNomenclature[] = $one;
                            }

                        } else {

                            $aUniaueName[] = $one->name;
                            $aFinanceNomenclature[] = $one;
                        }

                    } elseif( $one->name_en == 'Reference for submission upon request' ) {

                        $aUniaueName[] = $one->name;
                        $aFinanceNomenclature[] = $one;
                    }

                }
            }
        }


        // For transit students
        $transitClassAttendanceBought = false;
        if ( !empty(Auth::user()->studentProfile->category) && (Auth::user()->studentProfile->category == Profiles::CATEGORY_TRANSIT) ) {
            $transitClassAttendanceBought = StudentFinanceNomenclature::isBought(Auth::user()->id, FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID, Auth::user()->studentProfile->currentSemester());
        }

        return view('pages.helps',[
            'nomenclature' => $aFinanceNomenclature,
            'transitClassAttendanceBought' => $transitClassAttendanceBought,
            'locale' => $locale,
            'user' => $user,
            'boughtServiceIds' => $boughtServiceIds
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function qrTest()
    {
        return view('qr_test');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function cinema()
    {
        return view('pages.cinema');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function gameLibrary()
    {
        return view('pages.gameLibrary');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function courses()
    {
        return view('pages.courses');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hostel()
    {
        return view('pages.hostel');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mobilePhones()
    {
        return view('pages.mobilePhones');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function procoffee()
    {
        return view('pages.procoffee');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function career()
    {
        return view('pages.career');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rentBikes()
    {
        return view('pages.rentBikes');
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function payMemo()
    {
        return view('pages.pay_instructions');
    }

}
