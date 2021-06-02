@extends('layouts.clear')

@section('title', __('Авторизация'))

@section('content')
<div class="bg-info h-100">
    <div class="container d-flex flex-column h-100 w-md-50">
        <div class="h-auto my-auto">
            <div class="bg-white border p-5">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-label-group mb-3">
                        <input type="email" name="email" value="{{ old('email') }}" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Адрес электронной почты" autocomplete="email username" required autofocus>
                        <label for="email">E-mail</label>
                    </div>
                    <div class="form-label-group mb-3">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Пароль" autocomplete="current-password" required>
                        <label for="password">Пароль</label>
                    </div>
                    <div class="form-group mb-3">
                        <label for="remember">
                            <input type="checkbox" name="remember" id="remember" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
                            Запомнить меня
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-m">
                        Вход
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
