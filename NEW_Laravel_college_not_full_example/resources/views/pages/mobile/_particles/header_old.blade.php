<nav class="navbar navbar-default navbar-static-top">

            @guest
                <div class="col-md-12 text-right no-padding">
                    <a class="{{ Lang::locale()=='kz'?'active':'' }} btn btn-link" href="{{@getLangURI('kz')}}" style="color: #fff;">KZ</a>
                    <a class="{{ Lang::locale()=='ru'?'active':'' }} btn btn-link" href="{{@getLangURI('ru')}}" style="color: #fff;">RU</a>
                    <a class="{{ Lang::locale()=='en'?'active':'' }} btn btn-link" href="{{@getLangURI('en')}}" style="color: #fff;">EN</a>
                </div>
            @else
            <!-- Collapsed Hamburger -->

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false" style="top: 35px;">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <div class="collapse navbar-collapse" id="app-navbar-collapse" style="margin-left: auto;max-width: 1000px;margin-right: auto;">

                        <!-- Right Side Of Navbar -->

                            <ul class="nav navbar-nav navbar-expand-lg">
                                <!-- Authentication Links -->

                                    {{--<li><a href="{{ route('studentShopIndex') }}">{{__('Shop')}}</a></li>--}}

                                @if(\App\User::getCurrentRole() == \App\Role::NAME_CLIENT)
                                    <li><a href="{{ route('userProfile') }}">{{__('Profile')}}</a></li>

                                    {{--<li><a href="{{ route('userDocs') }}">{{__('Документы')}}</a></li>--}}

                                    <li><a href="{{ route('financesPanel') }}">{{__('Finances')}}</a></li>

                                    <li><a href="{{ route('study') }}">{{__('Study')}}</a></li>
                                    <li><a href="{{ route('deansOffice') }}">{{__('Dean\'s Office')}}</a></li>



                                @elseif(\App\User::getCurrentRole() == \App\Role::NAME_TEACHER)
                                    <li><a href="{{ route('teacherProfile') }}">{{__('Profile Teacher')}}</a></li>
                                    <li><a href="{{ route('teacherQrGenerate') }}">{{__('QR genetare')}}</a></li>
                                @endif
                                
                                {{--
                                @if(!\App\Services\Auth::user()->keycloak)
                                    <li><a href="{{ route('chatter.home') }}">{{__('Forum')}}</a></li>
                                    <li><a href="{{ route('help') }}">{{__('Help')}}</a></li>
                                    @if(\App\User::getCurrentRole() == \App\Role::NAME_CLIENT)
                                        <li><a href="{{ route('studentPromotionList') }}">{{__('Promotions')}}</a></li>
                                    @endif
                                @endif
                                --}}

                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                        {{ Auth::user()->name }} <span class="caret"></span>
                                    </a>

                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="{{ route('logout') }}"
                                               onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                                {{__('Logout')}}
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </li>
                            </ul>


                            <ul class="nav navbar-nav navbar-right navbar-expand-lg" style="height: 35px;">
                                <li><a class="{{ Lang::locale()=='ru'?'active':'' }}" href="{{@getLangURI('ru')}}">RU</a></li>
                                <li><a class="{{ Lang::locale()=='kz'?'active':'' }}" href="{{@getLangURI('kz')}}">KZ</a></li>
                                <li><a class="{{ Lang::locale()=='en'?'active':'' }}" href="{{@getLangURI('en')}}">EN</a></li>
                            </ul>


                </div>

            @endguest

            <!-- Branding Image -->
            <div class="col-md-12 text-center">
                <a class="navbar-brand @auth auth @endauth" href="{{ url('/') }}">
                    {{-- config('app.name', 'Laravel') --}}
                    <img src="{{ URL::asset('assets/img/logo-autumn.gif') }}" />
                </a>
            </div>


        @auth
        <!--<div class="collapse navbar-collapse" id="app-navbar-collapse">-->
            <!-- Left Side Of Navbar -->
            <!--<ul class="nav navbar-nav navbar-right">
                <li><a class="{{ Lang::locale()=='ru'?'active':'' }}" href="{{@getLangURI('ru')}}">RU</a></li>
                <li><a class="{{ Lang::locale()=='kz'?'active':'' }}" href="{{@getLangURI('kz')}}">KZ</a></li>
                <li><a class="{{ Lang::locale()=='en'?'active':'' }}" href="{{@getLangURI('en')}}">EN</a></li>
            </ul>-->



        <!--</div>-->
        @endauth

</nav>