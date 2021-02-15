<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light top-menu">
    <!-- Left navbar links -->
    <ul class="navbar-nav" style="margin-top:0px;">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item">
            <a href="{{ URL::to('/') }}" class="nav-link">
                <img class="miras-logo" src="{{ URL::asset('lte_assets/img/logo.png') }}" alt="{{getcong('site_name')}}" />
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link bvi-open"><i class="far fa-eye"></i></a>
        </li>
    </ul>

    @if(\App\Services\Auth::check())
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto" style="margin-top:0px;">
        <li class="nav-item">

            <a href="{{ route('logout') }}" style="margin-right:10px;">
                <i class="fas fa-sign-out-alt"></i>
            </a>

        </li>
    </ul>
    @endif
</nav>
<!-- /.navbar -->