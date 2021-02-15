<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @if (Request::secure() && Request::is('wifi') ) 
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    @endif

    <!-- PWA-->
      <link rel="manifest" href="{{ URL::asset('manifest.json') }}">
      <!-- Add to home screen for Safari on iOS -->
      <meta name="apple-mobile-web-app-capable" content="yes" />
      <meta name="apple-mobile-web-app-status-bar-style" content="black" />
      <meta name="apple-mobile-web-app-title" content="{{getcong('site_name')}}" />
      <link rel="apple-touch-icon" href="{{ URL::asset('images/miras-logo-512.png') }}" />
      <link rel="apple-touch-icon" sizes="152x152" href="{{ URL::asset('images/miras-logo-152.png') }}">
      <link rel="apple-touch-icon" sizes="167x167" href="{{ URL::asset('images/miras-logo-167.png') }}">
      <link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('images/miras-logo-180.png') }}">
      <!-- Windows -->
      <meta name="msapplication-TileImage" content="{{ URL::asset('images/miras-logo-512.png') }}" />
      <meta name="msapplication-TileColor" content="#363636" />
      <meta name="theme-color" content="#363636"/>
      <script type="text/javascript" src="{{ URL::asset('assets/js/app.js') }}"></script>

      <link rel="icon" type="image/png" href="{{ URL::asset('images/miras-logo-512.png') }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0 maximum-scale=1.0" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/ionicons.min.css') }}">
    <!-- Tempusdominus Bbootstrap 4 -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <link rel="stylesheet" type="text/css" href="{{ URL::asset('lte_assets/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('lte_assets/dist/css/bootstrap-datetimepicker.min.css') }}">

    <!-- JQVMap -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/jqvmap/jqvmap.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/jquery.dataTables.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/daterangepicker/daterangepicker.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/summernote/summernote-bs4.css') }}">
    <!-- Google Font: Source Sans Pro -->

    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/uikit.min.css') }}">

    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/mobile_style.css') }}">

    @yield('css')
    @if(Config::get('chatter.routes.home', null) != null)
        @if(Config::get('chatter.routes.home'))
            @if(Request::is( Config::get('chatter.routes.home') ) ||
                Request::is( '*/' . Config::get('chatter.routes.home') ) ||
                Request::is( Config::get('chatter.routes.home') . '/*' )  ||
                Request::is( '*/' . Config::get('chatter.routes.home') . '/*'
            ))
                <link rel="stylesheet" href="/assets/css/forum-custom.css" />
            @endif
        @endif
    @endif

    <link rel="stylesheet" href="{{ URL::asset('bvi.isvek/css/bvi.min.css') }}">
    <link rel="stylesheet" href="{{ URL::asset('bvi.isvek/css/bvi-font.min.css') }}">
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div id="app" class="wrapper" style="min-height:500px !important;">

    <!-- header and sidebar -->
    @include("_particles.header")

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper" style="min-height:900px;height:100% !important;">

        @if(Session::has('message'))
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="alert margin-20 {{ Session::get('alert-class', 'alert-info') }}">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {!! Session::get('message') !!}
                    </div>
                </div>
                <div class="col-md-2"></div>
            </div>
        @endif

        @if(Session::has('messages'))
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    @foreach(Session::get('messages') as $flashMessages)
                        <div class="alert margin-20 {{$flashMessages['class']}}">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            {!!$flashMessages['message']!!}
                        </div>
                    @endforeach
                    <?php session()->remove('messages'); ?>
                </div>
                <div class="col-md-2"></div>
            </div>
        @endif

        @if(Session::has('flash_message'))
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="alert alert-success margin-20">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {!! Session::get('flash_message') !!}
                    </div>
                    @if(Session::get('withoutBack') != true )
                        <a href="{{ route('home') }}" class="btn btn-info"> {{__('Back')}} </a>
                    @endif
                </div>
                <div class="col-md-2"></div>
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="container">
                <div class="row">
                    <div class="col-12 margin-t20">
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }} <br>
                                        @if(Session::has('resendUrl'))
                                            <a href="{{Session::get('resendUrl')}}">{{ __('Resend') }}</a>
                                            {{Session::flush()}}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @yield("content")

    </div>
    <!-- /.content-wrapper -->

    <!-- footer -->
    @include("_particles.footer")

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>

</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="{{ URL::asset('lte_assets/plugins/jquery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ URL::asset('lte_assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('lte_assets/dist/js/jquery.dataTables.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ URL::asset('lte_assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ URL::asset('lte_assets/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ URL::asset('lte_assets/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ URL::asset('lte_assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ URL::asset('lte_assets/plugins/jqvmap/maps/jquery.vmap.world.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ URL::asset('lte_assets/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ URL::asset('lte_assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('lte_assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ URL::asset('lte_assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ URL::asset('lte_assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ URL::asset('lte_assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ URL::asset('lte_assets/dist/js/adminlte.js') }}"></script>

<script src="{{ URL::asset('lte_assets/dist/js/bootstrap-select.min.js') }}"></script>
<script src="{{ URL::asset('lte_assets/dist/js/bootstrap-datetimepicker.min.js') }}"></script>

<script src="{{ URL::asset('lte_assets/dist/js/uikit.min.js') }}"></script>
<script src="{{ URL::asset('lte_assets/dist/js/uikit-icons.min.js') }}"></script>

<script src="{{ URL::asset('lte_assets/dist/js/script.js') }}"></script>
<script src="{{ URL::asset('lte_assets/dist/js/analytics.js?id=UA-7924497-32') }}"></script>

<script type="text/javascript">
    var langURL = '{{ App\Http\Middleware\LocaleMiddleware::getLocale() }}';
    if (langURL != '' ) langURL += '/';
</script>

@if (Request::is('bachelor/profile')
or Request::is('master/profile') )
    <script src="{{ URL::asset('assets/js/application.js') }}"></script>

@elseif(Request::is('profile/create') or Request::is('*/profile/create'))
    <script src="{{ URL::asset('assets/js/profile.js') }}"></script>
@endif

<script src="{{ URL::asset('assets/js/vue.js') }}"></script>
<script src="{{ URL::asset('admin_assets/js/axios.min.js') }}"></script>
<script src="/assets/js/bootstrap-vue.js"></script>
@yield('scripts')

@if((\App\User::getCurrentRole() == \App\Role::NAME_TEACHER)
    AND (Request::is('*question*') ) )
    <!-- include summernote css/js -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/summernote.css') }}">
    <script src="{{ URL::asset('lte_assets/dist/js/summernote.js') }}"></script>
@endif

@yield('js')

<!-- Global site tag (gtag.js) - Google Analytics -->
<!-- script async src="https://www.googletagmanager.com/gtag/js?id=UA-7924497-32"></script -->
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'UA-7924497-32');
</script>

<script src="{{ URL::asset('bvi.isvek/js/responsivevoice.min.js') }}"></script>
<script src="{{ URL::asset('bvi.isvek/js/js.cookie.js') }}"></script>
<script src="{{ URL::asset('bvi.isvek/js/bvi-init.js') }}"></script>
<script src="{{ URL::asset('bvi.isvek/js/bvi.min.js') }}"></script>



<link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/addtohomescreen.css') }}">
<script src="{{ URL::asset('assets/js/addtohomescreen.min.js') }}"></script>
<script>
addToHomescreen({
    //options
    autostart: true
});
</script>

@if(Auth::User() && !(Request::is('chat')))
    <script src="{{ URL::asset('/assets/js/chat/newMessagesTotalCounter.js') }}"></script>
@endif

</body>
</html>



