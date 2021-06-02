<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\Models\Pp;
use App\Notifications\VerifyEmail;
use App\Rules\AdvertiserUniqueRule;
use App\Rules\PpDomainUniqueRule;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Spatie\Honeypot\ProtectAgainstSpam;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * @param null|Pp $pp
     */
    public $pp;

    /**
     * @param null|User $user
     */
    public $user;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware(ProtectAgainstSpam::class);
        $this->pp = PartnerProgramStorage::getPP();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showRegistrationForm()
    {
        // Если это облачный домен - переадресуем на главную, так как регистрация там
        if (!$this->pp) {
            return redirect('/');
        }

        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // Выключаем автологин
        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    // public function redirectPath()
    // {
    // }

    /**
     * Get the post register / login redirect path.
     *
     * @return string
     */
    public function redirectTo()
    {
        return 'https://' . $this->pp->tech_domain . '/?success=pp.created';
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        if ($this->pp) {
            // Регистрация партнера
            $rules = $this->partnerValidatorRules($data);
        } else {
            $rules = $this->advertiserValidatorRules($data);
        }

        $customAttributes = [
            'name' => __('register-advert.fields.name.label'),
            'domain' => __('register-advert.fields.domain.label'),
            'email' => __('register-advert.fields.email.label'),
            'phone' => __('register-advert.fields.phone.label'),
            'policy' => __('register-advert.fields.policy.label'),
        ];

        return Validator::make($data, $rules, [], $customAttributes);
    }

    /**
     * Get a validator rules for partner role
     *
     * @return array
     */
    public function partnerValidatorRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('users')->where(function (Builder $query) {
                    return $query->where('pp_id', '=', $this->pp->id);
                })
            ],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get a validator rules for advertiser role
     *
     * @return array
     */
    public function advertiserValidatorRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'min:2', 'max:63', new PpDomainUniqueRule],
            'email' => ['required', 'string', 'email', 'max:255', new AdvertiserUniqueRule],
            'phone' => ['nullable', 'string'],
            'policy' => ['accepted'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        // Если в поле пароль ничего не введено, генерируем его
        if (empty($data['password'])) {
            $data['password'] = Str::random(8);
        }

        $this->generated_password = $data['password'];

        if ($this->pp) {
            // Регистрация партнера
            return $this->createPartner($data);
        }

        // Регистрация рекламодателя
        return $this->createAdvertiser($data);
    }

    /**
     * Create a new partner after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function createPartner(array $data)
    {
        $this->user = new User;
        $this->user->name = $data['name'];
        $this->user->email = $data['email'];
        $this->user->pp_id = $this->pp->id;
        $this->user->role = 'partner';
        $this->user->password = Hash::make($data['password']);
        $this->user->hash_name = $this->getUserHash();
        $this->user->auth_token = hash('sha256', Str::random(60));
        $this->user->save();

        return $this->user;
    }

    /**
     * Create a new advertiser and partner program after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    public function createAdvertiser(array $data)
    {
        $this->user = new User;
        $this->user->name = $data['name'];
        $this->user->email = $data['email'];
        $this->user->phone = $data['phone'] ?? null;
        $this->user->role = 'advertiser';
        $this->user->password = Hash::make($data['password']);
        $this->user->hash_name = $this->getUserHash();
        $this->user->auth_token = hash('sha256', Str::random(60));
        $this->user->save();

        $this->pp = new Pp;
        $this->pp->user_id = $this->user->id;
        $this->pp->tech_domain = Str::lower($data['domain'] . '.' . config('app.domain'));
        $this->pp->demo_ends_at = now()->addWeeks(2);
        $this->pp->onboarding_status = 'registered';
        $this->pp->lang = [
            'en' => true,
            'es' => false,
            'ru' => true,
        ];
        $this->pp->save();

        $this->user->pp_id = $this->pp->id;
        $this->user->save();
        $this->user->load('pp');

        return $this->user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        $locale = Cookie::get('locale', 'ru');
        $notify = (new VerifyEmail($this->generated_password, $locale));
        $this->user->notify($notify);
    }

    public function getUserHash()
    {
        $hash_name = substr(md5(time()), random_int(2, 20), 6);
        if (User::query()->where('hash_name', $hash_name)->exists()) {
            $this->getUserHash();
        }

        return $hash_name;
    }
}
