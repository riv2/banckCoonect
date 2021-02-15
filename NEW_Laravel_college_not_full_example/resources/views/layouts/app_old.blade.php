<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/css/fontawesome-stars.css') }}">

    <link href="{{ URL::asset('assets/css/style.css') }}" rel="stylesheet">
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

</head>
<body>
    <div id="app">
        @include("_particles.header_old")

        @if(Session::has('message'))
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                      <div class="alert {{ Session::get('alert-class', 'alert-info') }}">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        {!! Session::get('message') !!}
                      </div>
                    </div>
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
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="alert alert-success">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                {!! Session::get('flash_message') !!}
                            </div>
                            @if(Session::get('withoutBack') != true )
                                <a href="{{ route('home') }}" class="btn btn-primary"> {{__('Back')}} </a>
                            @endif
                        </div>
                    </div>
                </div>
                @if(Session::get('withoutBack') != true )
                    {{Session::flush()}}
                @endif
            @endif

            @if (count($errors) > 0)
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
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

            @yield('content')

            @include("_particles.footer")
    </div>

    <!-- Scripts -->
    <script src="{{ URL::asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/jquery-datatable.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <script src="{{ URL::asset('assets/js/jquery.barrating.min.js') }}"></script>

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
    <script src="/assets/js/tableHorizontalScroll.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    @yield('scripts')

    @if((\App\User::getCurrentRole() == \App\Role::NAME_TEACHER)
        AND (Request::is('*question*') ) )
                <!-- include summernote css/js -->
                <link rel="stylesheet" href="{{ URL::asset('lte_assets/dist/css/summernote.css') }}">
                <script src="{{ URL::asset('lte_assets/dist/js/summernote.js') }}"></script>
    @endif

    @yield('js')


    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script src="{{ URL::asset('lte_assets/dist/js/analytics.js?id=UA-7924497-32') }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-7924497-32');
    </script>


    @if(Auth::User() && (\App\User::getCurrentRole() == \App\Role::NAME_TEACHER) && !(Request::is('chat')))
        <script src="{{ URL::asset('/assets/js/chat/newMessagesTotalCounter.js') }}"></script>
    @endif

</body>
</html>
