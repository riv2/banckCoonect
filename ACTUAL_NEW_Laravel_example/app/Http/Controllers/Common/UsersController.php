<?php

namespace App\Http\Controllers\Common;

use App\Filters\UserFilter;
use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\Lists\PartnerStatusesList;
use App\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public $page_title = 'Список пользователей';
    public $role = null;

    public function index(UserFilter $filters)
    {
        $partners = User::query()
            ->filter($filters)
            ->when(auth()->user()->role !== 'manager', function (Builder $query, $value) {
                $query->where('pp_id', '=', PartnerProgramStorage::getPP()->id);
            })
            ->when(!is_null($this->role), function (Builder $query) {
                $query->where('role', '=', $this->role);
            })
            ->orderBy('id', 'desc')
            ->with('pp')
            ->paginate(50);

        return view('common.users.index', [
            'page_title' => $this->page_title,
            'fields' => $filters->fields,
            'partners' => $partners,
            'role' => $this->role,
            'statuses' => PartnerStatusesList::getList(),
        ]);
    }

    public function create()
    {
        return view('common.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                Rule::unique('users')->where(function ($query) use ($request) {
                    return $query->where('email', $request->email)->where('pp_id', auth()->user()->pp_id);
                }),
            ],
        ]);

        $generated_password = Str::random(8);

        $this->user = new User;
        $this->user->name = $request->name;
        $this->user->email = $request->email;
        $this->user->pp_id = auth()->user()->pp_id;
        $this->user->role = 'partner';
        $this->user->password = Hash::make($generated_password);
        $this->user->hash_name = $this->getUserHash();
        $this->user->auth_token = hash('sha256', Str::random(60));
        $this->user->save();

        event(new Registered($this->user));

        $locale = Cookie::get('locale', 'ru');
        $notify = (new VerifyEmail($generated_password, $locale));
        $this->user->notify($notify);
    }

    public function show(User $user)
    {
        return view(auth()->user()->role . '.users.show', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        /** @var \App\User */
        $user->load('pay_methods');

        Validator::make(
            $request->all(),
            [
                'name' => 'required|string',
                'phone' => 'string|nullable',
                'skype' => 'string|nullable',
                'pay_method_id' => 'required|numeric|in:' . $user->pp->pay_methods->pluck('id')->join(','),
                'company_name' => 'required_if:pay_method_id,1',
                'company_inn' => 'required_if:pay_method_id,1',
                'bank_company_account' => 'required_if:pay_method_id,1',
                'bank_identifier_code' => 'required_if:pay_method_id,1',
                'bank_beneficiary' => 'required_if:pay_method_id,1',
                'bank_correspondent_account' => 'required_if:pay_method_id,1',
                'cc_number' => ['required_if:pay_method_id,2', new \LVR\CreditCard\CardNumber],
                'webmoney_number' => 'required_if:pay_method_id,3',
            ],
            [
                'required' => 'Поле :attribute обязательно для заполнения',
                'required_if' => 'Поле :attribute обязательно для заполнения',
            ],
            [
                'pay_method_id' => __('profile.fields.pay_method_id'),
                'company_name' => __('profile.fields.company_name'),
                'company_inn' => __('profile.fields.company_inn'),
                'bank_company_account' => __('profile.fields.bank_company_account'),
                'bank_identifier_code' => __('profile.fields.bank_identifier_code'),
                'bank_beneficiary' => __('profile.fields.bank_beneficiary'),
                'bank_correspondent_account' => __('profile.fields.bank_correspondent_account'),
                'cc_number' => __('profile.fields.cc_number'),
                'webmoney_number' => __('profile.fields.webmoney_number'),
            ]
        )->validate();

        $user->name = $request->get('name');
        $user->phone = $request->get('phone');
        $user->skype = $request->get('skype');

        info('Партнер обновил профиль!', $user->getDirty());
        $user->save();

        $keys = ['pay_method_id'];
        $keys = array_merge($keys, array_keys(User::$pay_method_fields[$request->pay_method_id]));

        /** @var array текущий метод оплаты */
        $old_pay_method = $user->pay_method ? $user->pay_method->only($keys) : [];

        /** @var array введенный метод оплаты */
        $new_pay_method = $request->only($keys);

        // Если не равен предыдущему, сохраняем
        if ($old_pay_method != $new_pay_method) {
            info('Партнер обновил реквизиты!', array_diff($old_pay_method, $new_pay_method));
            // Удаляем в корзину все старые методы оплаты
            $user->pay_methods()->update([
                'deleted_at' => now(),
            ]);
            $user->pay_methods()->attach($request->pay_method_id, $new_pay_method);
            return redirect()->back()->withSuccess('Реквизиты успешно обновлены!');
        }

        return redirect()->back()->withSuccess('Профиль успешно сохранен!');
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
