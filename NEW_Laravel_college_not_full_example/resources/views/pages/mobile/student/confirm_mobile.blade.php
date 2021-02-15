<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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

    <nav class="navbar navbar-default navbar-static-top">
        <div class="col-md-12 text-right no-padding">
            <a class="{{ Lang::locale()=='kz'?'active':'' }} btn btn-link" href="{{@getLangURI('kz')}}" style="color: #fff;">KZ</a>
            <a class="{{ Lang::locale()=='ru'?'active':'' }} btn btn-link" href="{{@getLangURI('ru')}}" style="color: #fff;">RU</a>
            <a class="{{ Lang::locale()=='en'?'active':'' }} btn btn-link" href="{{@getLangURI('en')}}" style="color: #fff;">EN</a>
        </div>

        <!-- Branding Image -->
        <div class="col-md-12 text-center">
            <a class="navbar-brand @auth auth @endauth" href="{{ url('/') }}">
                {{-- config('app.name', 'Laravel') --}}
                <img src="{{ URL::asset('assets/img/logo-autumn.gif') }}" />
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('Phone confirmation') }}</div>
                    <div class="panel-body">
                        <div class="alert alert-danger" style="display: none;" v-show="error">@{{ error }}</div>
                        <div class="col-md-12" v-if="!codeSended">

                            @if(!empty(\App\Services\Auth::user()->studentProfile->mobile))
                                <p>{{ __('Check your phone number. If you have a different number, please indicate') }}.</p>
                            @endif

                            @if(empty(\App\Services\Auth::user()->studentProfile->mobile))
                                <p>{{ __('Enter your mobile number') }}.</p>
                            @endif

                            <input type="text" v-model="phone" class="form-control" value="{{ \App\Services\Auth::user()->studentProfile->mobile }}" required />

                        </div>

                        <div class="col-md-12" v-show="codeSended" style="display: none;">
                            @if(!empty(\App\Services\Auth::user()->studentProfile->mobile))
                                <p>{{ __('Enter the code received in the SMS message') }}:</p>
                            @endif

                            <input type="text" v-model="code" class="form-control" value="" required />
                        </div>

                        <div class="col-md-12" style="margin-top: 10px;" v-if="!codeSended">
                            <a class="btn btn-primary center-block" v-on:click="sendConfirmCode()">{{ __('Confirm') }}</a>
                        </div>

                        <div class="col-md-12" style="margin-top: 10px; display: none;" v-show="codeSended">
                            <a class="btn btn-primary center-block" v-on:click="verifyCode()">{{ __('Check code') }}</a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

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

<script type="text/javascript">

    var app = new Vue({
        el: '#app',
        data: {
            phone: '{{ \App\Services\Auth::user()->studentProfile->mobile ?? '' }}',
            codeSended: false,
            code: '',
            error: ''
        },
        methods: {
            sendConfirmCode: function()
            {
                var self = this;
                axios.post('{{route('profileMobile')}}', {
                    mobile: this.phone
                }, {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                })
                    .then(function(response){
                        if(response.data.status == 'success') {
                            self.codeSended = true;
                            self.error = '';
                        }
                        else {
                            if(response.data.status == 'redirect')
                            {
                                location.href = '/';
                            } else
                            {
                                self.error = response.data.message;
                            }
                        }
                    });
            },

            verifyCode: function()
            {
                var self = this;
                axios.post('{{route('profileMobileDoubleApprove')}}', {
                    code: this.code
                }, {
                    'X-CSRF-TOKEN': $('meta[name=csrf-token]').attr('content')
                })
                    .then(function(response){
                        if(response.data.status == 'success') {
                            location.href = '/';
                        }
                        else {
                            self.error = response.data.message;
                        }
                    });
            }
        }
    });

</script>

@if((\App\User::getCurrentRole() == \App\Role::NAME_TEACHER)
    AND (Request::is('*question*') ) )
    <!-- include summernote css/js -->
    <link rel="stylesheet" href="{{ URL::asset('lte_assets/plugins/summernote/summernote-bs4.css') }}">
    <script src="{{ URL::asset('lte_assets/plugins/summernote/summernote-bs4.min.js') }}"></script>
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

</body>
</html>