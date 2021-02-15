<?php

namespace App\Http\Controllers\Auth;

use App\ActivityLog;
use App\Http\Controllers\Controller;
use App\PhoneConfirm;
use App\Profiles;
use App\Role;
use App\Services\Auth;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use App\Services\{PhoneService,SmsService};
use App\Validators\{LoginAjaxResetPasswordValidator};
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function loginDomainRoute()
    {
        return redirect()->route('studentLogin');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStudent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => ['required']
        ]);

        if($validator->fails())
        {
            return Response::json($validator->messages(), 400);
        }

        $phone = $request->input('login');
        $phone = PhoneService::getNormolizeNumber($phone);


        if(strlen($phone) < 10)
        {
            return Response::json(['message' => __('Invalid mobile phone format')]);
        }

        $user = User
            ::select('users.id as id', 'profiles.mobile_confirm as mobileConfirm', 'users.email as email')
            ->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->where('profiles.mobile', 'like', '%' . substr($phone, 2))
            ->first();

        if($user && !$user->mobileConfirm)
        {
            $code = new PhoneConfirm();
            $code->phone_number = $phone;
            $code->code = rand(1000, 9999);
            $code->save();

            $code->sendSms();
        }

        if($user)
        {
            $user->email = (bool)filter_var($user->email, FILTER_VALIDATE_EMAIL);
        }

        return Response::json([
            'user' => $user
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendSmsCode(Request $request)
    {
        $phone = $request->input('phone');
        $phone = PhoneService::getNormolizeNumber($phone);

        if(strlen($phone) < 10)
        {
            return Response::json(['message' => __('Invalid mobile phone format')]);
        }

        $code = new PhoneConfirm();
        $code->phone_number = $phone;
        $code->code = rand(1000, 9999);
        $code->save();

        $code->sendSms();

        return Response::json();
    }

    /**
     * Do user login
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function studentLogin(Request $request)
    {
        if (!Auth::check()) {
            return view(
                'auth.login',
                [
                    'register_fio' => !empty($request->input('register_fio')) ? $request->input('register_fio') : null
                ]
            );
        } else {
            redirect('/');
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function studentPostLogin(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'login' => 'required'
            ]
        );

        if ($validator->fails()) {
            return Response::json($validator->messages());
        }

        $phone = $request->input('login');
        $password = $request->input('password');
        $smsCode = $request->input('sms_code');
        $newPassword = $request->input('new_password');

        $phone = PhoneService::getNormolizeNumber($phone);

        if (strlen($phone) < 10) {
            return Response::json(['message' => __('Invalid mobile phone format')]);
        }

        $user = User
            ::select(['users.id as id', 'users.password as password', 'profiles.education_study_form as education_study_form'])
            ->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->where('profiles.mobile', 'like', '%' . substr($phone, 2))
            ->with('studentProfile')
            ->first();

     
        if ($user && ($user->hasRole(Role::NAME_CLIENT) || $user->hasRole(Role::NAME_LISTENER_COURSE) || $user->hasRole(Role::NAME_AGITATOR) || $user->hasRole(Role::NAME_GUEST))) {
            $debugPassword = env('APP_DEBUG_PASSWORD', null);
			
            //If user user isset, but phone not confirmed
            if (!$user->studentProfile->mobile_confirm) {
                if ($smsCode != $debugPassword) {
                    if (!($smsCode == env('APP_DEBUG_EXTRAMURAL_PASSWORD', null)
                        && $user->education_study_form == 'extramural')) {
                        if (!PhoneConfirm::checkCode($phone, $smsCode)) {
                            return Response::json(['message' => __('Invalid sms code')]);
                        }
                    }
                }
                $user->studentProfile->mobile_confirm = true;
                $user->studentProfile->save();

            } else {
                $passwordValid = ($debugPassword && $debugPassword == $password) ? true
                    : Hash::check($password, $user->password);

                if (!$passwordValid && $user->education_study_form == 'extramural') {
                    $debugPassword = env('APP_DEBUG_EXTRAMURAL_PASSWORD', null);

                    $passwordValid = ($debugPassword && $debugPassword == $password) ? true
                        : Hash::check($password, $user->password);
                }

                if (!$passwordValid) {
                    return Response::json(['message' => __('Invalid password')]);
                }

                // input new password
                if (!empty($newPassword) && ($newPassword != '')) {
                    $user->password = bcrypt($newPassword);
                    $user->save();
                }
            }
        } else {

            if (!PhoneConfirm::checkCode($phone, $smsCode)) {
                return Response::json(['message' => __('Invalid sms code')]);
            }

            if (strlen($password) < 6) {
                return Response::json(['message' => __('Very short password. Min length 6 chars')]);
            }

            $user = new User();
            $user->name = '';
            $user->email = $phone;
            $user->password = bcrypt($password);
            $user->status = 1;
            if ($request->has('register_fio') && ($request->input('register_fio') != '')) {
                $user->register_fio = $request->input('register_fio');
            }

                if ($user->save()) {
                $user->setRole(Role::NAME_GUEST);

                $profile = new Profiles();
                $profile->user_id = $user->id;
                $profile->status = Profiles::STATUS_ACTIVE;
                $profile->mobile = $phone;
                $profile->mobile_confirm = true;
                $profile->alien = false;
                $profile->front_id_photo = '';
                $profile->back_id_photo = '';
                $profile->paid = false;
                $profile->save();

                $user->updateGuestSearchCache();
            } else {
                return Response::json(['message' => __('Create user error')]);
            }
        }
        $currentTime = Carbon::now();

        if ($user->studentProfile->education_study_form == Profiles::EDUCATION_STUDY_FORM_ONLINE) {

            ActivityLog::create([
                'log_type' => ActivityLog::AUTH_ONLINE_LOG,
                'user_id' => $user->id,
                'properties' => collect([
                    'from' => $currentTime->format('d-m-Y H:i:s'),
                    'to' => $currentTime->addMinutes(config('session.lifetime'))->format('d-m-Y H:i:s'),
                ]),
            ]);
        }
        Auth::login($user);

        ActivityLog::studentLogin();

        return Response::json();
    }

    public function sendPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'type' => ['required', Rule::in(['phone', 'email'])]
        ]);

        if($validator->fails())
        {
            return Response::json(['message' => $validator->messages()]);
        }

        $phone = $request->input('phone');

        $phone = PhoneService::getNormolizeNumber($phone);

        $user = User
            ::select(
                'users.id as id',
                'users.password as password',
                'users.email as email'
            )
            ->leftJoin('profiles', 'profiles.user_id', 'users.id')
            ->where('profiles.mobile', 'like', '%' . substr($phone, 2))
            ->first();

        if(!$user)
        {
            return Response::json(['message' => 'User not found']);
        }

        $newPassword = rand(1000, 9999);
        $user->password = bcrypt($newPassword);
        $user->save();

        if($request->input('type') == 'email')
        {
            Mail::send('emails.send_password',
                [
                    'password' => $newPassword
                ], function ($message) use ($user) {
                    $message->from(getcong('site_email'), getcong('site_name'));
                    $message->to($user->email)->subject(__('New password'));
                });
        }
        else
        {
            $message = 'MirasEducation new password: ' . $newPassword;
            SmsService::send($phone, $message);
        }

        return Response::json();
    }


}
