<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @yield('title')
        @isset ($partner_program)
            - {{ $partner_program->long_name ?? $partner_program->short_name }}
        @else
            - GoCPA.cloud
        @endisset
    </title>
    @yield("css")
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    @if(isset(auth()->user()->pp->favicon))
        <link rel="shortcut icon" type="image/ico" href="{{ auth()->user()->pp->favicon }}">
    @else
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/site.webmanifest">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5d26d9">
    @endif
    <meta name="apple-mobile-web-app-title" content="GoCPA">
    <meta name="application-name" content="GoCPA">
    <meta name="msapplication-TileColor" content="#5d26d9">
    <meta name="theme-color" content="#ffffff">
    <style>
        :root {
            --bs-primary: {{ auth()->user()->pp->color1 ?? '#5d26d9'}};
            --bs-hover-color: {{ auth()->user()->pp->color2 ?? '#5d26d9'}};
            --bs-menu-bg-color: {{ auth()->user()->pp->color3 ?? '#2f4050'}};
            --bs-menu-bg-active-color: {{ auth()->user()->pp->color4 ?? '#293846'}};
        }
    </style>
</head>
<body>
<div class="container-main">
    <div class="sidenav border-right">
        <div class="sidenav__top">
            <div class="position-relative">
                <span class="fa-stack fa-2x" style="vertical-align: top;color:#a7b1c2">
                    <i class="fas fa-circle fa-stack-2x"></i>
                    <i class="fas fa-user fa-stack-1x fa-inverse"></i>
                </span>

                @if (Route::has(auth()->user()->role . '.profile.index'))
                <a href="{{ route(auth()->user()->role . '.profile.index') }}" class="d-block mt-1 font-weight-bold text-decoration-none text-truncate stretched-link" style="color: #DFE4ED">
                    #{{ auth()->id() }} {{ auth()->user()->email }}
                </a>
                @else
                <span class="d-block mt-1 font-weight-bold text-decoration-none text-truncate" style="color: #DFE4ED">
                    #{{ auth()->id() }} {{ auth()->user()->email }}
                </span>
                @endif

                @if (auth()->user()->role !== 'partner')
                <div class="text-xs d-block text-muted font-weight-bold">
                    {{ \App\Role\UserRole::getRoleList()[auth()->user()->role] }}
                </div>
                @endif
            </div>
        </div>
        @if (auth()->user()->role=='advertiser' && auth()->user()->pp->onboarding_status=='registered')
            <div class="sidenav__bottom"></div>
        @else
            <div class="sidenav__bottom">
                {!! Menu::main() !!}
            </div>
        @endif
    </div>
    <div class="mainblock">
        <div class="container-fluid" style="height: 61px;">
            <div class="row h-100 py-2 align-content-center">
                <div class="col">
                    @switch(auth()->user()->role)
                        @case('manager')
                        @lang('Кабинет менеджера')
                        @break
                        @case('advertiser')
                        @lang('Кабинет рекламодателя')
                        @break
                        @case('partner')
                        @lang('Кабинет партнера')
                        @break
                    @endswitch
                    @if (env('APP_ENV') !== 'production')
                        <span class="small text-secondary">
                                Enviroment: {{ env('APP_ENV') }}
                            @if ($gitcommitcomposer_branch)
                                Branch: {{ $gitcommitcomposer_branch }}
                            @endif
                            @if ($gitcommitcomposer_commit)
                                Commit: {{ $gitcommitcomposer_commit }}
                            @endif
                            </span>
                    @endif
                </div>
                @if(auth()->user()->role == 'advertiser' || auth()->user()->role == 'partner')
                    <?php $countLangs = 0 ?>
                    @foreach(auth()->user()->pp->lang as $item)
                        @if($item)
                            <?php $countLangs += 1; ?>
                        @endif
                    @endforeach
                    @if($countLangs != 1)
                        <div class="col-auto text-end small">
                            <ul class="navbar-nav ml-auto">
                                <li class="nav-item dropdown">
                                <span class="dropdown-toggle text-uppercase" href="#" data-bs-toggle="dropdown">
                                    {{ app()->getLocale() }} <span class="caret"></span>
                                </span>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        @foreach(config('app.locales') as $key => $item)
                                            @if(auth()->user()->pp->lang[$key])
                                                <a class="dropdown-item text-uppercase @if (app()->getLocale() === $key) disabled @endif"
                                                   href="{{ route('locale', ['locale' => $key]) }}?redirect={{ request()->url() }}">{{ $key }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endif
                @else
                    <div class="col-auto text-end small">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item dropdown">
                                <span class="dropdown-toggle text-uppercase" href="#" data-bs-toggle="dropdown">
                                    {{ app()->getLocale() }} <span class="caret"></span>
                                </span>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @foreach(config('app.locales') as $key => $item)
                                        <a class="dropdown-item text-uppercase @if (app()->getLocale() === $key) disabled @endif"
                                           href="{{ route('locale', ['locale' => $key, 'redirect' => request()->url()]) }}">{{ $key }}</a>
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                @endif

                <div class="col-auto text-end small">
                    @impersonating
                    <span class="d-none d-lg-inline">@lang('Режим просмотра кабинета партнёра')</span>
                    <a href="{{ route('impersonate.leave') }}"
                       class="link-secondary font-weight-bold text-decoration-none">
                        <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                    </a>
                    @else
                        <a href="{{ route('logout') }}" class="link-secondary font-weight-bold text-decoration-none"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt"></i> {{ __('app.logout') }}
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                              style="display: none;">@csrf</form>
                        @endImpersonating
                </div>
            </div>
        </div>
        <div class="container-fluid mb-3 py-3 bg-white border-top border-bottom">
            <h1 class="mb-2">@yield("title")</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb p-0 mb-0">
                    <li class="breadcrumb-item"><a href="/">{{ __('app.home') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@yield("title")</li>
                </ol>
            </nav>
        </div>
        <div class="container-fluid">
            @include("widgets.alerts")
            @yield("content")
        </div>
    </div>
</div>

@yield("js")
<script src="{{ mix('js/app.js') }}" async defer></script>

@if ((config('app.gocpa_project') == 'cloud'))
@env('production')
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function (m, e, t, r, i, k, a) {
            m[i] = m[i] || function () {
                (m[i].a = m[i].a || []).push(arguments)
            };
            m[i].l = 1 * new Date();
            k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
        })
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(67127008, "init", {
            clickmap: true,
            trackLinks: true,
            accurateTrackBounce: true,
            webvisor: true
        });
    </script>
    <noscript>
        <div><img src="https://mc.yandex.ru/watch/67127008" style="position:absolute; left:-9999px;" alt=""/></div>
    </noscript>
    <!-- /Yandex.Metrika counter -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-165563525-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-165563525-1');
    </script>
    <script
        src='https://cdn.experrto.io/client/experrto.js'></script>
    <script>
        Experrto.identify("ff6f6d58ae95e07cff4150d583e48284815e65ac")
    </script>
@endenv
@endif
</body>

</html>
