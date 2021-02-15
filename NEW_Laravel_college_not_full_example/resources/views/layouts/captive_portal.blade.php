<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

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

    <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/desctop_style.css') }}">

    @yield('css')
    
</head>
<body class="hold-transition sidebar-mini layout-fixed">

<div id="app" class="wrapper" style="min-height:500px !important;">

    <!-- header and sidebar -->
    {{--@include("_particles.header")--}}

    @if(Session::has('message'))
        <div class="content-wrapper height-auto">
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
        </div>
    @endif

    @if(Session::has('messages'))
        <div class="content-wrapper height-auto">
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
        </div>
    @endif

    @if(Session::has('flash_message'))
        <div class="content-wrapper height-auto">
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
        </div>
    @endif

    @if (count($errors) > 0)
        <div class="content-wrapper height-auto">
            <div class="row no-margin">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <div class="alert alert-danger margin-20">
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
                <div class="col-md-2"></div>
            </div>
        </div>
    @endif


    <!-- Content Wrapper. Contains page content -->
    {{--<div class="content-wrapper" style="height:100% !important;">--}}

        <div class="clearfix navbar top-menu">
            <ul class="nav nav-treeview float-right">
                <li class="nav-item">
                    <a href="{{@getLangURI('kz')}}" class="nav-link {{ Lang::locale()=='kz'?'d-none':'' }}">
                        <p class="no-margin">KZ</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{@getLangURI('ru')}}" class="nav-link {{ Lang::locale()=='ru'?'d-none':'' }}">
                        <p class="no-margin">RU</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{@getLangURI('en')}}" class="nav-link {{ Lang::locale()=='en'?'d-none':'' }}">
                        <p class="no-margin">EN</p>
                    </a>
                </li>
            </ul>
        </div>

        @yield("content")

{{--</div>--}}
    <!-- /.content-wrapper -->

    <!-- footer -->
    {{--@include("_particles.footer")--}}

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

</body>
</html>



