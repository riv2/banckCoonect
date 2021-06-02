@extends('layouts.clear')

@section('title', __('Регистрация рекламодателя'))

@section('content')
    <!-- Start gocpa.gocpa.cloud TRACK -->
    <script>
        !function(e,t,p,c,a,n,o){e[c]||((a=e[c]=function(){a.process?a.process.apply(a,arguments):a.queue.push(arguments)}).queue=[],a.t=+new Date,(n=t.createElement(p)).async=1,n.src="https://gocpa.gocpa.cloud/openpixel.min.js?t="+864e5*Math.ceil(new Date/864e5),(o=t.getElementsByTagName(p)[0]).parentNode.insertBefore(n,o))}(window,document,"script","gocpa");
        gocpa("init","https://gocpa.gocpa.cloud/cpapixel.gif");
        gocpa("event","pageload");

        function gocpa_purchase() {
            var email = document.getElementById('email').value;
            gocpa("event", "purchase", {
                order_id: email,
            });
        }
    </script>
    <!-- End gocpa.gocpa.cloud TRACK  -->

    <div class="container-fluid h-100 loginpage p-0">
        <div class="container">
            <div class="row">
                <div class="col-11 pt-1">
                    <a href="/"><img src="https://gocpa.ru/img/logo_text_dark.png" alt="" ></a>
                </div>
                <div class="col-1 text-end pt-3">
                    @if ((config('app.gocpa_project') == 'cloud'))
                    <span class="dropdown">
                        <a id="navbarDropdown" class="dropdown-toggle text-uppercase text-white" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-decoration: none">
                            {{ app()->getLocale() }} <span class="caret"></span>
                        </a>
                        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-end" style="min-width:70px">
                            @foreach(config('app.locales') as $key => $item)
                                <a class="dropdown-item text-uppercase @if (app()->getLocale() === $key) disabled @endif"
                                   href="{{ route('locale', ['locale' => $key]) }}">{{ $key }}</a>
                            @endforeach
                        </div>
                    </span>
                    @endif
                </div>
            </div>

            <div class="row loginmargin">
                <div class="col-12 col-md-4 mb-5">

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        @honeypot

                        <div class="loginleft__text">@lang('register-advert.welcome')</div>

                        <div class="form-label-group mb-3">
                            <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="@lang('register-advert.fields.name.placeholder')" autocomplete="name" required>
                            <label for="domain">@lang('register-advert.fields.name.label')</label>
                            @error('name')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-label-group mb-3">
                            <div class="input-group">
                                <input type="text" name="domain" value="{{ old('domain') }}" id="domain" class="form-control @error('domain') is-invalid @enderror" placeholder="@lang('register-advert.fields.domain.placeholder')" autocomplete="off" required
                                       pattern="[A-z0-9](?:[A-z0-9\-]{0,61}[A-z0-9])?"
                                       minlength="2"
                                       maxlength="63"
                                       data-bs-container="body"
                                       data-bs-toggle="popover"
                                       data-bs-trigger="focus"
                                       data-bs-placement="bottom"
                                       data-bs-content="@lang('register-advert.fields.domain.popover')">
                                <span class="input-group-text">.{{ config('app.domain') }}</span>
                            </div>
                            <label for="domain">@lang('register-advert.fields.domain.label')</label>
                            @error('domain')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-label-group mb-3" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="auto" data-bs-content="@lang('register-advert.fields.email.popover')">
                            <input type="email" name="email" value="{{ old('email') }}" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="@lang('register-advert.fields.email.placeholder')" autocomplete="email" required>
                            <label for="email">@lang('register-advert.fields.email.label')</label>

                            @error('email')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-label-group mb-3" data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus" data-bs-placement="auto" data-bs-content="@lang('register-advert.fields.phone.popover')">
                            <input type="text" name="phone" value="{{ old('phone') }}" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="@lang('register-advert.fields.phone.placeholder')" autocomplete="phone">
                            <label for="phone">@lang('register-advert.fields.phone.label')</label>

                            @error('phone')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group login-options">
                            <div class="col-sm-12 ">
                                <p class="checkbox">
                                    <label for="iAntiSpanRule">
                                        <input class="d-inline-block p-5" type="checkbox" checked id="iAntiSpanRule" name="policy">
                                            <span   style="font-size: 0.8rem">@lang('register-advert.fields.policy.agree')</span>
                                            <a href="@lang('register-advert.fields.policy.link_privacy')" style="font-size: 0.8rem">@lang('register-advert.fields.policy.privacy')</a>
                                            <span   style="font-size: 0.8rem">@lang('register-advert.fields.policy.and')</span>
                                            <a href="@lang('register-advert.fields.policy.link_legal')" style="font-size: 0.8rem">@lang('register-advert.fields.policy.legal')</a>
                                    </label>
                                </p>
                                @error('policy')
                                <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group d-flex">
                            <button type="submit" class="btn btn-primary mx-auto" onclick="gocpa_purchase()">
                                @lang('register-advert.fields.submit')
                            </button>
                        </div>
                    </form>
                    <br>
                    <div class="text-end"><a href="/already_registered">@lang('register-advert.already-registered')</a></div>
                </div>
                <div class="col-md-2">
                </div>
                <div class="col-12 col-md-6 logintooltips">
                    <div class="mx-auto">
                        <figure>
                            <img src="@lang('register-advert.screen.image')" alt="" class="img-fluid rounded shadow mb-4 loginright__image">
                            <figcaption class="text-center">
                                <div class="small text-info text-white">@lang('register-advert.right.0.text-info')</div>
                                <div class="loginright__header text-white mb-2">@lang('register-advert.right.0.loginright-header')</div>
                                <div class="loginright__text text-white">@lang('register-advert.right.0.loginright-text')</div>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="navbar fixed-bottom">
        <div class="container pb-3">
            <div class="row">
                <a href="@lang('register-advert.navbar_a')">@lang('register-advert.navbar_a_text')</a>
            </div>
        </div>
    </div>

@endsection
