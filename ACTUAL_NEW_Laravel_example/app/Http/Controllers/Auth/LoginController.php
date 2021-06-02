<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\Models\EnterLog;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'loginAsID']);
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showLoginForm()
    {
        if (in_array(request()->getHost(), config('domain.clouds.advert_reg_domains'))) {
            return view('gocpa_cloud/auth/login-advert', [
                'hidenav' => true,
            ]);
        } elseif (PartnerProgramStorage::getPP()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // Добавляем pp_id в request
        $pp_id = PartnerProgramStorage::getPP()->id ?? null;
        if (!empty($pp_id)) {
            $request->request->add([
                'pp_id' => $pp_id,
            ]);
            return $request->only($this->username(), 'password', 'pp_id');
        } else {
            if (config('domain.cloud_domain')) {
                $request->request->add([
                    'role' => ['advertiser', 'manager'],
                ]);
                return $request->only($this->username(), 'password', 'role');
            } else {
                return $request->only($this->username(), 'password');
            }
        }
    }

    /**
     * Get the post register / login redirect path.
     * @return string
     */
    public function redirectPath($auth_from_cloud = false)
    {
        $user = auth()->user();
        $role = $user->role;
        $pp = PartnerProgramStorage::getPP();
        $pp_id = ($pp) ? $pp->id : null;
        if ($role == "advertiser" && $pp_id != $user->pp_id) {
            $user->auth_token = hash('sha256', Str::random(60));
            $user->save();
            $domain = $user->pp->prod_domain ?? $user->pp->tech_domain;
            return "https://" . $domain . "/login/by_token/" . $user->auth_token . "?auth_from_cloud=1";
        }
        $append = ($auth_from_cloud) ? "?auth_from_cloud=1" : "";
        return route($role . '.report') . $append;
    }

    public function loginAs()
    {
        return redirect('/' . auth()->user()->role . '/report');
    }

    public function loginAsID(Request $request, $user)
    {
        if (! $request->hasValidSignature(false)) {
            abort(401);
        }
        if (config('app.gocpa_project') == 'cpadroid') {
            return redirect(route('impersonate', ['id' => $user]));
        }

        $result = auth()->loginUsingId($user, true);

        return redirect('/' . $result->role . '/report');
    }

    public function tokenlogin(Request $request)
    {
        if (empty($request->token)) {
            return redirect("/");
        }
        $user = User::where('auth_token', '=', $request->token)->firstOrFail();
        $auth = $this->guard()->loginUsingId($user->id, true);
        /** @phpstan-ignore-next-line */
        if ($auth) {
            $user->auth_token = null;
            $user->save();
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            if ($response = $this->authenticated($request, $this->guard()->user())) {
                return $response;
            }
            $request->session()->put("auth_by_token", true);
            return redirect()->intended($this->redirectPath($request->get("auth_from_cloud", false)));
        }
        /** @phpstan-ignore-next-line */
        return redirect("/");
    }

    public function onetimelogin(Request $request)
    {
        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            /** @phpstan-ignore-next-line */
            return $this->sendLockoutResponse($request);
        }

        $user = User::where('auth_token', '=', $request->token)->firstOrFail();
        $auth = $this->guard()->loginUsingId($user->id, true);
        /** @phpstan-ignore-next-line */
        if ($auth) {
            $user->auth_token = null;
            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }
            $user->save();
            $request->session()->regenerate();
            $this->clearLoginAttempts($request);
            if ($response = $this->authenticated($request, $this->guard()->user())) {
                return $response;
            }
            return $request->wantsJson()
                ? new Response('', 204)
                : redirect()->intended($this->redirectPath($user->role));
        }
    }

    /**
     * The user has been authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if (!$user->email_verified_at) {
            $user->email_verified_at = now();
            $user->save();
            // if (env('APP_ENV') === 'production' || env('APP_ENV') === 'prod') {
            $locale = Cookie::get('locale', 'ru');

            $message = [];
            if ($user->role === 'advertiser') {
                $message[] = '☁️ Заявка на регистрацию новой партнерской программы!';
            } elseif ($user->role === 'partner') {
                $message[] = '🌤️ В одной из партнерских программ зарегистрировался новый партнёр!';
            }
            $message[] = 'ПП: #' . $user->pp->id . ' https://' . $user->pp->pp_domain;
            $message[] = 'Его ID: #' . $user->getKey();
            $message[] = 'Имя: ' . $user->name;
            $message[] = 'E-mail: ' . $user->email;
            if ($user->phone) {
                $message[] = 'Контакт для связи: ' . $user->phone;
            }
            $message[] = 'Выбранная локаль: ' . $locale;

            $text = join(PHP_EOL, $message);
            $token = config('telegram-logger.token');
            if (!app()->runningUnitTests() && $token) {
                $url = 'https://api.telegram.org/bot' . $token . '/sendMessage';
                \Illuminate\Support\Facades\Http::post($url, [
                        'text' => $text,
                        'chat_id' => config('telegram-logger.chat_id'),
                        'parse_mode' => 'html'
                    ]);
            }
            // }
        }

        EnterLog::create([
            'datetime' => now(),
            'user_id' => $user->id,
            'result' => 'success',
            'ip' => $request->getClientIp(),
            'ua' => $request->header('User-Agent'),
            'request' => 'IPs: ' . join(',', $request->getClientIps()),
        ]);
    }
}
