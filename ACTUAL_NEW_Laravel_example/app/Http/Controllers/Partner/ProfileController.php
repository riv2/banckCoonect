<?php

namespace App\Http\Controllers\Partner;

use App\Helpers\PartnerProgramStorage;
use App\Http\Controllers\Controller;
use App\User;
use App\Lists\TaxationSystemList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(): \Illuminate\Contracts\Support\Renderable
    {
        /** @var \App\User */
        $user = auth()->user();

        return view('partner.profile.index', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user): \Illuminate\Http\RedirectResponse
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
                'contract_number' => 'required_if:pay_method_id,1',
                'contract_date' => 'required_if:pay_method_id,1|date',
                'bank_company_account' => 'required_if:pay_method_id,1',
                'bank_identifier_code' => 'required_if:pay_method_id,1',
                'bank_beneficiary' => 'required_if:pay_method_id,1',
                'bank_correspondent_account' => 'required_if:pay_method_id,1',
                'vat_tax' => 'required_if:pay_method_id,1',
                'taxation_system' => 'required_if:pay_method_id,1',
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
                'contract_number' => __('profile.fields.contract_number'),
                'contract_date' => __('profile.fields.contract_date'),
                'bank_company_account' => __('profile.fields.bank_company_account'),
                'bank_identifier_code' => __('profile.fields.bank_identifier_code'),
                'bank_beneficiary' => __('profile.fields.bank_beneficiary'),
                'bank_correspondent_account' => __('profile.fields.bank_correspondent_account'),
                'vat_tax' => __('profile.fields.vat_tax'),
                'taxation_system' => __('profile.fields.taxation_system'),
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
        // dd($old_pay_method);

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

    public function update_password(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validate($request, [
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, auth()->user()->{$attribute})) {
                        $fail(__('Старый пароль не совпадает!'));
                    }
                }
            ],
            'new_password' => 'required|confirmed|min:8',
        ]);

        /** @var \App\User */
        $user = auth()->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()
            ->back()
            ->withSuccess(['Пароль успешно изменен!']);
    }
}
