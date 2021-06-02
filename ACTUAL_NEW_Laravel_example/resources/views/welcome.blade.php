<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        @if (isset($partner_program))
            {{ $partner_program->long_name ?? $partner_program->tech_domain }}
        @else
            {{ config('app.name', 'Partners') }}
        @endif
    </title>
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 84px;
        }

        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }

        .form-group.row {
            margin-bottom: 10px;
        }

        .m-b-md {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @if ((config('app.gocpa_project') == 'cloud'))
            <span class="dropdown-toggle text-uppercase" href="#" data-bs-toggle="dropdown"
                  style="font-size: 13px; font-weight: 600">
                  {{ app()->getLocale() }} <span class="caret"></span>
            </span>
            <div class="dropdown-menu dropdown-menu-end">
                @foreach(config('app.locales') as $key => $item)
                    <a class="dropdown-item text-uppercase @if (app()->getLocale() === $key) disabled @endif"
                       style="font-size: 13px; font-weight: 600"
                       href="{{ route('locale', ['locale' => $key, 'redirect' => request()->url()]) }}?redirect={{ request()->url() }}">{{ $key }}</a>
                @endforeach
            </div>
            @endif
            @auth
                <a href="{{ auth()->user()->getMainPageUrl() }}"><span class="fa fa-user"></span> Войти</a>
            @else

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">{{ __("advertiser.welcome.registration") }}</a>
                @endif
            @endauth
        </div>
    @endif

    <div class="content" style="min-width:500px;">
        <div class="title m-b-md">
            @if (isset($partner_program))
                @if($partner_program->logo)
                    <img src="{{ $partner_program->logo }}" alt="" class="img-fluid" style="max-width:250px">
                @else
                    {{ $partner_program->short_name }}
                @endif
            @else
            @endif
        </div>
        @if (request()->get('success'))
            <div class="alert alert-success">
                @lang('success.' . request()->get('success'))
            </div>
        @endif
        <div style="font-size:24px; margin-bottom:16px;">{{ __('advertiser.welcome.affiliate-program') }}</div>
        @include("widgets.alerts")
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('advertiser.welcome.log-in') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">Email</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                       class="col-md-4 col-form-label text-md-right">{{ __('advertiser.welcome.pass') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror" name="password"
                                           required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember"
                                               id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            {{ __('advertiser.welcome.remember-me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __("advertiser.welcome.submit-2") }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('advertiser.welcome.forgot-pass') }}?
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@yield("js")
<script src="{{ mix('js/app.js') }}" async defer></script>
</body>
</html>
