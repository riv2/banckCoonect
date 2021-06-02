<?php

namespace App\Http\Controllers\Analyst;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index(): \Illuminate\Contracts\Support\Renderable
    {
        /** @var \App\User */
        $user = auth()->user();

        return view('analyst.profile', [
            'user' => $user,
        ]);
    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'string|nullable',
            'skype' => 'string|nullable',
            'pay_account' => 'string|nullable',
        ]);

        /** @var \App\User */
        $user = auth()->user();
        $user->name = $request->get('name');
        $user->phone = $request->get('phone');
        $user->skype = $request->get('skype');
        //$user->pay_account = $request->get('pay_account');
        $user->save();

        return redirect()
            ->route('advertiser.profile')
            ->withSuccess(['Профиль успешно сохранен!']);
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
