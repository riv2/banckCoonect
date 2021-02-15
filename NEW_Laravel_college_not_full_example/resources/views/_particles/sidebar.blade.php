<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-5" id="sidebaring">
    <!-- Sidebar user panel (optional) -->
    @if( false && \auth()->user() )
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                @if( (Auth::user()->studentProfile) && !empty(Auth::user()->studentProfile->faceimg) )
                    <img src="{{ URL::asset('lte_assets/img/user.png') }}" class="img-circle elevation-2" alt="" />
                <!--
                <img
                        class="img-circle elevation-2"
                        src="{{ \App\Services\Avatar::getStudentFacePublicPath(Auth::user()->studentProfile->faceimg) ?? '' }}"
                        alt=" {{ Auth::user()->studentProfile->fio ?? '' }}"
                        style="max-width: 160px; max-height:160px;"
                />
                -->
                @else
                    <img src="{{ URL::asset('lte_assets/img/user.png') }}" class="img-circle elevation-2" alt="" />
                @endif
            </div>
            <div class="info">
                <a href="#" class="d-block text-white"> {{ Auth::user()->name ?? '' }} </a>
            </div>
        </div>
@endif

<!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @if(\App\Services\Auth::check())

                    @if( (\auth()->user()) && \auth()->user()->hasRole('guest') )
                        <li class="nav-item">
                            <a href="{{ route('userProfile') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('userProfile') }}">
                                <i class="nav-icon fas fas fa-id-card"></i>
                                <p>
                                    {{ __('Profile') }}
                                </p>
                            </a>
                        </li>
                    @endif

                    @if( (\auth()->user()) && \auth()->user()->hasRole('client') )
                        <li class="nav-item has-treeview menu-open">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-graduation-cap"></i>
                                <p>
                                    {{ __('student') }}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="display:block;">
                                <li class="nav-item">
                                    <a href="{{ route('userProfile') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('userProfile') }}">
                                        <i class="fas fa-id-card"></i>
                                        <p> {{__('Profile')}} </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('financesPanel') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('financesPanel') }}">
                                        <i class="far fa-credit-card"></i>
                                        <p> {{__('Finances')}} </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('study') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('study') }}">
                                        <i class="fas fa-university"></i>
                                        <p> {{__('Study')}} </p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route('deansOffice') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('deansOffice') }}">
                                        <i class="fas fa-building"></i>
                                        <p> {{__('Dean\'s Office')}} <span class="badge badge-secondary">{{ \auth()->user()->getNotificationCount() }}</span> </p>
                                    </a>
                                </li>

                            </ul>
                        </li>
                    
                    <li class="nav-item">
                        <a href="{{ route('student.news.show', ['info_type' => 'important']) }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('student.news.show') }}">
                            <i class="fas fa-newspaper"></i>
                            <p> {{__('Schedule')}} </p>
                        </a>
                    </li>
                    
                    @endif

                    @if( \auth()->user() && ( \auth()->user()->hasRole('guest') || ( \auth()->user()->hasRole('guest') && \auth()->user()->hasRole('agitator') ) ) )
                        <li class="nav-item">
                            <a href="{{route('training')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('training') }}">
                                <i class="nav-icon fas fa-user-graduate"></i>
                                <p>
                                    {{ __('Study') }}
                                </p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{route('chatter.home')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('chatter.home') }}">
                            <i class="nav-icon far fa-comments"></i>
                            <p>
                                {{ __('Forum') }}
                            </p>
                        </a>
                    </li>
                    @if( (\auth()->user()) && \auth()->user()->hasRole('client') )
                        <li id="student-sidebar-link" class="nav-item">
                            <a href="{{ route('openChat') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('openChat') }}">
                                <i class="nav-icon fas fa-comment"></i>
                                <p>
                                    {{ __('Chat') }}
                                    <span id="student-chat_new-messages" class="badge badge-primary ml-1" style="display: none;"></span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('helps')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('helps') }}">
                                <i class="nav-icon fas fa-info-circle"></i>
                                <p>
                                    {{ __('Helps') }}
                                </p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{route('wifi')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('wifi') }}">
                            <i class="nav-icon fas fa-wifi"></i>
                            <p>
                                Wifi
                            </p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{route('gid')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('gid') }}">
                            <i class="nav-icon fas fa-info"></i>
                            <p>
                                {{ __('GID') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('bus')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('bus') }}">
                            <i class="nav-icon fas fa-bus"></i>
                            <p>
                                {{ __('Bus') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('fitnessRoom')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('fitnessRoom') }}">
                            <i class="nav-icon fas fa-dumbbell"></i>
                            <p>
                                {{ __('Fitness room') }}
                            </p>
                        </a>
                    </li>
                    {{--
                    <li class="nav-item">
                        <a href="{{route('cafeteria')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('cafeteria') }}">
                            <i class="nav-icon fas fa-coffee"></i>
                            <p>
                                {{ __('Cafeteria') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('pool')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('pool') }}">
                            <i class="nav-icon fas fa-swimmer"></i>
                            <p>
                                {{ __('Pool') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('studentCheckin')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('qr') }}">
                            <i class="nav-icon fas fa-qrcode"></i>
                            <p>
                                QR
                            </p>
                        </a>
                    </li>
                    {{--
                    <li class="nav-item">
                        <a href="{{route('cinema')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('cinema') }}">
                            <i class="nav-icon fas fa-film"></i>
                            <p>
                                {{ __('Cinema') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('gameLibrary')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('gameLibrary') }}">
                            <i class="nav-icon fas fa-gamepad"></i>
                            <p>
                                {{ __('Game Library') }}
                            </p>
                        </a>
                    </li>
                    --}}
                    <li class="nav-item">
                        <a href="{{route('getCoursesList')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('getCoursesList') }}">
                            <i class="nav-icon fab fa-discourse"></i>
                            <p>
                                {{ __('Courses') }}
                            </p>
                        </a>
                    </li>
                    
                    {{--
                    <li class="nav-item">
                        <a href="{{route('hostel')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('hostel') }}">
                            <i class="nav-icon fas fa-hotel"></i>
                            <p>
                                {{ __('Housing') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('mobilePhones')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('mobilePhones') }}">
                            <i class="nav-icon fas fa-mobile-alt"></i>
                            <p>
                                {{ __('Mobile phones') }}
                            </p>
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a href="{{route('procoffee')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('procoffee') }}">
                            <i class="nav-icon fas fa-coffee"></i>
                            <p>
                                Procoffee
                            </p>
                        </a>
                    </li>
                    --}}
                    <li class="nav-item">
                        <a href="{{route('library.page')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('library.page') }}">
                            <i class="fas fa-book-open"></i>
                            <p>
                                {{ __('Library') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('student.info.show', ['info_type' => 'important']) }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('student.info.show') }}">
                            <i class="fas fa-newspaper"></i>
                            <p>
                                {{ __('Infodesk') }}
                            </p>
                        </a>
                    </li>

                    {{--
                    <li class="nav-item">
                        <a href="{{route('career')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('career') }}">
                            <i class="nav-icon fas fa-briefcase"></i>
                            <p>
                                {{ __('Career') }}
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{route('rentBikes')}}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('rentBikes') }}">
                            <i class="nav-icon fas fas fa-biking"></i>
                            <p>
                                {{ __('Rent bikes') }}
                            </p>
                        </a>
                    </li>
                    --}}
                    <li class="nav-item">
                        <a href="{{ route('students.polls.show') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('students.polls.show') }}">
                            <i class="nav-icon fas fas fa-poll"></i>
                            <p>
                                {{ __('Polls') }}
                            </p>
                        </a>
                    </li>

                    <li class="nav-item has-treeview" style="display: none;">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-graduation-cap"></i>
                            <p>
                                {{ __('Work') }}
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">

                            <li class="nav-item">
                                <a href="{{ route('vacancy.index') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('vacancy.index') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p>
                                        {{ __('Vacancies') }}
                                    </p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('vacancy.resume') }}" class="nav-link {{ \App\Services\SidebarMenuActive::isActive('vacancy.resume') }}">
                                    <i class="fas fa-clipboard-list"></i>
                                    <p>
                                        {{ __('My Resume') }}
                                    </p>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif

                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-globe"></i>
                        <p>
                            {{ __('Language') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{@getLangURI('kz')}}" class="nav-link {{ Lang::locale()=='kz'?'active':'' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>KZ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{@getLangURI('ru')}}" class="nav-link {{ Lang::locale()=='ru'?'active':'' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>RU</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{@getLangURI('en')}}" class="nav-link {{ Lang::locale()=='en'?'active':'' }}">
                                <i class="nav-icon far fa-circle"></i>
                                <p>EN</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @if( (\auth()->user()) )
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i>
                            <p>
                                {{ __('Logout') }}
                            </p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

