
        <div class="row banners-bottom-menu no-margin margin-b10">
            @if( (\auth()->user()) && \auth()->user()->hasRole('client') )
                <div class="col-3 item">
                    <a href="{{ route('student.news.show', ['info_type' => 'important']) }}">
                        <h3 class="font-size20"><i class="fas fa-newspaper text-danger"></i></h3>
                        <p class="text-danger font-size12">{{ __('Schedule') }}</p>
                    </a>
                </div>
            @endif        

            @if( \auth()->user() && ( \auth()->user()->hasRole('guest') || ( \auth()->user()->hasRole('guest') && \auth()->user()->hasRole('agitator') ) ) )
             <div class="col-3 item">
                 <a href="{{route('training')}}">
                    <h3><i class="fas fa-user-graduate text-danger"></i></h3>
                    <p class="text-danger">{{ __('Study') }}</p>
                 </a>
             </div>
            @endif
            
            @if( (\auth()->user()) && \auth()->user()->hasRole('client') )
                <div class="col-3 item">
                    <a href="{{route('helps')}}">
                        <h3><i class="fas fa-info-circle text-danger"></i></h3>
                        <p class="text-danger">{{ __('Helps') }}</p>
                    </a>
                </div>
            @endif
             <div class="col-3 item">
                 <a href="{{route('wifi')}}">
                     <h3><i class="fas fa-wifi text-danger"></i></h3>
                     <p class="text-danger">Wifi</p>
                 </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('gid')}}">
                     <h3><i class="fas fa-info text-danger"></i></h3>
                     <p class="text-danger">{{ __('GID') }}</p>
                 </a>
             </div>

             <div class="col-3 item">
                 <a href="{{route('bus')}}">
                     <h3><i class="fas fa-bus text-danger"></i></h3>
                     <p class="text-danger">{{ __('Bus') }}</p>
                 </a>
             </div>

             <div class="col-3 item">
                 <a href="{{route('fitnessRoom')}}">
                     <h3><i class="fas fa-dumbbell text-danger"></i></h3>
                     <p class="text-danger">{{ __('Fitness room') }}</p>
                 </a>
             </div>
             {{--
             <div class="col-3 item">
                 <a href="{{route('cafeteria')}}">
                     <h3><i class="fas fa-coffee text-danger"></i></h3>
                     <p class="text-danger">{{ __('Cafeteria') }}</p>
                 </a>
             </div>
             --}}
             <div class="col-3 item">
                <a href="{{route('pool')}}">
                    <h3><i class="fas fa-swimmer text-danger"></i></h3>
                    <p class="text-danger">{{ __('Pool') }}</p>
                </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('studentCheckin')}}">
                     <h3><i class="fas fa-qrcode text-danger"></i></h3>
                     <p class="text-danger">QR</p>
                 </a>
             </div>

             {{--
             <div class="col-3 item">
                <a href="{{route('cinema')}}">
                    <h3><i class="fas fa-film text-danger"></i></h3>
                    <p class="text-danger">{{ __('Cinema') }}</p>
                </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('gameLibrary')}}">
                     <h3><i class="fas fa-gamepad text-danger"></i></h3>
                     <p class="text-danger">{{ __('Game Library') }}</p>
                 </a>
             </div>
             --}}
             <div class="col-3 item">
                 <a href="{{route('getCoursesList')}}">
                     <h3><i class="fab fa-discourse text-danger"></i></h3>
                     <p class="text-danger">{{ __('Courses') }}</p>
                 </a>
             </div>
             {{--
             <div class="col-3 item">
                 <a href="{{route('hostel')}}">
                     <h3><i class="fas fa-hotel text-danger"></i></h3>
                     <p class="text-danger">{{ __('Housing') }}</p>
                 </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('mobilePhones')}}">
                     <h3><i class="fas fa-mobile-alt text-danger"></i></h3>
                     <p class="text-danger">{{ __('Mobile phones') }}</p>
                 </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('procoffee')}}">
                     <h3 class="font-size20"><i class="fas fa-mug-hot text-danger"></i></h3>
                     <p class="text-danger">Procoffee</p>
                 </a>
             </div>
             --}}
             <div class="col-3 item">
                <a href="{{route('library.page')}}">
                    <h3 class="font-size20"><i class="fas fa-book-open text-danger"></i></h3>
                    <p class="text-danger font-size12">{{ __('Library') }}</p>
                </a>
             </div>

            <div class="col-3 item">
                <a href="{{ route('student.info.show', ['info_type' => 'important']) }}">
                    <h3 class="font-size20"><i class="fas fa-newspaper text-danger"></i></h3>
                    <p class="text-danger font-size12">{{ __('Infodesk') }}</p>
                </a>
            </div>

             {{--
             <div class="col-3 item">
                 <a href="{{route('career')}}">
                     <h3><i class="fas fa-briefcase text-danger"></i></h3>
                     <p class="text-danger">{{ __('Career') }}</p>
                 </a>
             </div>
             <div class="col-3 item">
                 <a href="{{route('rentBikes')}}">
                     <h3><i class="fas fa-biking text-danger"></i></h3>
                     <p class="text-danger">{{ __('Rent bikes') }}</p>
                 </a>
             </div>
             --}}

            {{--<div class="col-3 item">
                <a href="{{ route('openChat') }}">
                    <h3 class="font-size20"><i class="far fa-comment text-danger"></i></h3>
                    <span id="teacher-chat_new-messages" class="badge badge-primary ml-1" style="display: none; background-color: #007bff; position: absolute; bottom: 0; right: 0"></span>
                    <p class="text-danger font-size12">{{ __('Chat') }}</p>
                </a>
            </div>--}}

        </div>

        <div class="padding-20 margin-t15">
            {{--
            <div class="row no-margin margin-b15">
                <a href="{{ route('enterQrPage') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-hotel"> </i> {{ __('Enter') }} @if(Lang::locale()=='ru') &nbsp; @endif </a>
            </div>
            --}}
            <div class="row no-margin margin-b15">
                <a href="{{ route('userProfile') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-id-card"> </i> {{ __('Profile') }} @if(Lang::locale()=='ru') &nbsp; @endif </a>
            </div>
            @if( !\auth()->user() || (\auth()->user() && \auth()->user()->hasRole('client')) )
            <div class="row no-margin margin-b15">
                <a href="{{ route('financesPanel') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="far fa-credit-card"></i> {{ __('Finances') }} @if(Lang::locale()=='ru') &nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
            </div>
            <div class="row no-margin margin-b15">
                <a href="{{ route('study') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-university"></i> {{ __('Study') }} @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
            </div>
            <div class="row no-margin margin-b15">
                <a href="{{ route('deansOffice') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-building"></i> {{__('Dean\'s Office')}} @if(Lang::locale()=='ru') &nbsp;&nbsp;&nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp; @endif </a>
            </div>
            @endif
            @if( !\auth()->user() || (\auth()->user() && \auth()->user()->hasRole('listener_course')) )
                <div class="row no-margin margin-b15">
                    <a href="{{ route('getCourseCabinet') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="far fa-circle"></i> {{ __('Cabinet') }} @if(Lang::locale()=='ru') &nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
                </div>
            @endif
            @if( !\auth()->user() || (\auth()->user() && \auth()->user()->hasRole('agitator')) )
                <div class="row no-margin margin-b15">
                    <a href="{{ route('agitatorProfile') }}" class="btn btn-danger btn-lg btn-block education-btn">  @if(Lang::locale()=='ru') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif <i class="fas fa-users-cog"></i> {{ __('Profile agitator') }} </a>
                </div>
            @endif
        </div>



