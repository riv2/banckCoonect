<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light top-menu">
    <!-- Left navbar links -->
    <ul class="navbar-nav" style="margin-top:0px;">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <a href="{{ route('home') }}" class="nav-link">
                <img class="miras-logo" src="{{ URL::asset('lte_assets/img/logo.png') }}" alt="{{getcong('site_name')}}" />
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link bvi-open">@lang('Version for visually impaired')</a>
        </li>
    </ul>

    <ul class="navbar-nav" style="margin-top:0px;">

    </ul>
@if(\App\Services\Auth::check())
    <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto" style="margin-top:0px;">
            <li class="nav-item">
                <a href="{{@getLangURI('kz')}}" class="nav-link {{ Lang::locale()=='kz'?'disabled':'' }}">
                    <p>KZ</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{@getLangURI('ru')}}" class="nav-link {{ Lang::locale()=='ru'?'disabled':'' }}">
                    <p>RU</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{@getLangURI('en')}}" class="nav-link {{ Lang::locale()=='en'?'disabled':'' }}">
                    <p>EN</p>
                </a>
            </li>
            <li class="nav-item">
                <p style="margin:7px 10px 0px 10px;"> | </p>
            </li>
            <li class="nav-item">
                <a href="{{ route('logout') }}">
                    <p style="margin:8px 10px 0px 10px;"><i class="fas fa-sign-out-alt"></i></p>
                </a>
            </li>
        </ul>
    @endif
</nav>
<!-- /.navbar -->