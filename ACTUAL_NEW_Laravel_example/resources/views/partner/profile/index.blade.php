<?php
/* @var App\User $user */
?>
@extends('layouts.app')

@section('title', __('profile.app-title'))

@section('content')

<form action="{{ route(request()->user()->role . '.users.update', $user) }}" method="post" novalidate>
    @csrf
    @method('PUT')

    <x-box>
        <x-slot name="title">@lang('profile.app-title')</x-slot>
        <x-slot name="rightblock">
            {!! $user->impersonate_link_button !!}
        </x-slot>

        <div class="row">
            <div class="col-md">
                <div class="form-label-group mb-0">
                    <input type="email" name="email" value="{{ $user->email }}" class="form-control" @role(['partner']) readonly disabled @endrole>
                    <label>@lang('profile.fields.email')</label>
                </div>
            </div>
            <div class="col-md">
                <div class="form-label-group mb-0">
                    <input type="text" name="created_at" value="{{ $user->created_at->format('d F Y') }}" class="form-control" readonly disabled>
                    <label>@lang('profile.fields.created_at')</label>
                </div>
            </div>
            @if (Route::has(request()->user()->role . '.profile.update_password'))
            <div class="col-md">
                <a href="{{ route(request()->user()->role . '.profile.update_password', $user) }}" class="btn btn-outline-primary"><i class="fas fa-key"></i> Изменить пароль</a>
            </div>
            @endif
        </div>
    </x-box>

    <div class="row">
        @if ($user->pp && $user->pp->pay_methods)
        <x-box class="col-md-6">
            <x-slot name="title">@lang('profile.pay_method-title')</x-slot>

            @if (!$user->pay_method)
            <div class="alert alert-warning">@lang('profile.pay_method-empty')</div>
            @endif

            <div class="form-label-group mb-3">
                <select name="pay_method_id" class="form-select @error('pay_method_id') is-invalid @enderror" required onchange="profile_select_pay_method(this)">
                    <option value="">@lang('profile.choose_option')</option>
                    @foreach ($user->pp->pay_methods as $pp_pay_method)
                    <option value="{{ $pp_pay_method->id }}" {{ old('pay_method_id', ($user->pay_method->pay_method_id ?? null)) == $pp_pay_method->id ? 'selected' : '' }}>
                        {{ $pp_pay_method->caption }}
                    </option>
                    @endforeach
                </select>
                <label>@lang('profile.fields.pay_method_id')</label>
            </div>

            @foreach (App\User::$pay_method_fields as $pay_method_id => $pay_method_fields)
            @php
                $is_shown = old('pay_method_id', ($user->pay_method->pay_method_id ?? null)) == $pay_method_id;
                $reqdis = (old('pay_method_id', ($user->pay_method->pay_method_id ?? null)) == $pay_method_id) ? 'required' : 'disabled';
            @endphp
                <div class="pt-4 js-profile-pay_method_fields js-profile-pay_method_fields-{{ $pay_method_id }} @if (!$is_shown) d-none @endif">
                    @foreach ($pay_method_fields as $field_name => $field_settings)
                    @php
                        $value = old($field_name, $user->pay_method->{$field_name} ?? null);
                    @endphp
                        @switch($field_settings['type'] ?? 'text')
                            @case('date')
                            @case('text')
                            <div class="form-label-group mb-3">
                                <input type="{{ $field_settings['type'] }}" {!! $field_settings['attrs'] ?? '' !!} name="{{ $field_name }}" value="{{ $value }}" class="form-control @error($field_name) is-invalid @enderror" {{ $reqdis }}>
                                <label>@lang('profile.fields.' . $field_name)</label>
                            </div>
                            @break
                            @case('cc-number')
                            <div class="form-label-group mb-3">
                                <input type="tel" inputmode="numeric" pattern="[0-9\\s]{13,19}" autocomplete="cc-number" maxlength="19" placeholder="xxxx xxxx xxxx xxxx"  name="{{ $field_name }}" value="{{ $value }}" class="form-control @error($field_name) is-invalid @enderror" {{ $reqdis }}>
                                <label>@lang('profile.fields.' . $field_name)</label>
                            </div>
                            @break
                            @case('select')
                            <div class="form-label-group mb-3">
                                <select name="{{ $field_name }}" class="form-select @error($field_name) is-invalid @enderror" {{ $reqdis }}>
                                    <option value="">@lang('[выберите вариант]')</option>
                                    @foreach ($field_settings['options'] as $option_value => $option_text)
                                    <option value="{{ $option_value }}" @if ($value == $option_value) selected @endif>{{ $option_text }}</option>
                                    @endforeach
                                </select>
                                <label>@lang('profile.fields.' . $field_name)</label>
                            </div>
                            @break
                        @endswitch
                    @endforeach
                </div>
            @endforeach

        </x-box>
        @endif

        <x-box class="col-md-6">
            <x-slot name="title">Ваш профиль</x-slot>
            @foreach (\App\User::$fields as $field_name => $field_settings)
            <div class="form-label-group @if(!$loop->last) mb-3 @endif">
                <input type="{{ $field_settings['type'] }}" name="{{ $field_name }}" value="{{ old($field_name, $user->{$field_name} ?? null) }}" class="form-control @error($field_name) is-invalid @enderror" {{ $field_settings['required'] ? ' required' : '' }}>
                <label>@lang('profile.fields.' . $field_name)</label>
            </div>
            @endforeach
        </x-box>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-primary">{{ __('profile.save') }}</button>
    </div>

    {{--
    <div class="row">
        <x-box class="col-md-6 offset-md-3">
            <x-slot name="title">Изменить пароль</x-slot>

            <div class="d-none">
                <input type="text" name="username" autocomplete="username" value="{{ $user->email }}">
            </div>

            @role(['partner'])
            <div class="form-label-group mb-3">
                <input type="password" name="password" class="form-control" autocomplete="current-password" required>
                <label>@lang('Введите старый пароль')</label>
            </div>
            @endrole

            <p class="form-text mb-3">@lang('Пароль должен состоять из восьми или более символов латинского алфавита, содержать заглавные и строчные буквы, цифры. Для усложнения рекомендуем добавить символы и знаки препинания')</p>

            <div class="form-label-group mb-3">
                <input type="password" name="new_password" class="form-control" autocomplete="new-password" minlength="8" required>
                <label>@lang('Новый пароль')</label>
            </div>

            <div class="form-label-group mb-3">
                <input type="password" name="new_password_confirmation" class="form-control" autocomplete="new-password" minlength="8" required>
                <label>@lang('Новый пароль ещё раз')</label>
            </div>

            <div class="mt-auto text-end">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>

        </x-box>
    </div>
     --}}
</form>
    <script>
        function profile_select_pay_method(select)
        {
            var selectedElement = select.options[select.selectedIndex];
            var items = document.querySelectorAll('.js-profile-pay_method_fields');
            for (var i = 0; i < items.length; i++) {
                items[i].classList.add('d-none');

                var fields = items[i].querySelectorAll('input,select');
                for (var fi = 0; fi < fields.length; fi++) {
                    fields[fi].required = false;
                    fields[fi].disabled = true;
                }
            }
            var f = document.querySelector('.js-profile-pay_method_fields-' + selectedElement.value);
            f.classList.remove('d-none');

            var fields = f.querySelectorAll('input,select');
            for (var fi = 0; fi < fields.length; fi++) {
                fields[fi].required = true;
                fields[fi].disabled = false;
            }
        }
    </script>
    @endsection
