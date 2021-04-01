
@if( \auth()->user() )
    <div class="col-12 text-center margin-10 padding-15">
        @if(\App\Services\Auth::user()->hasRole('client'))
            <a href="{{route('studentPayToBalance')}}">
                <img src="{{ URL::asset('/lte_assets/img/pay_click_banner.jpg') }}" />
            </a>
        @else
            <img src="{{ URL::asset('/lte_assets/img/pay_banner.jpg') }}" />
        @endif
    </div>
@endif

<div class="row banners-bottom-menu no-margin">
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
                <h3 class="font-size20"><i class="fas fa-user-graduate text-danger"></i></h3>
                <p class="text-danger font-size12">{{ __('Study') }}</p>
            </a>
        </div>
    @endif

    @if( (\auth()->user()) && \auth()->user()->hasRole('client') )
        <div class="col-3 item">
            <a href="{{route('helps')}}">
                <h3 class="font-size20"><i class="fas fa-info-circle text-danger"></i></h3>
                <p class="text-danger font-size12">{{ __('Helps') }}</p>
            </a>
        </div>
    @endif
    <div class="col-3 item">
        <a href="{{route('wifi')}}">
            <h3 class="font-size20"><i class="fas fa-wifi text-danger"></i></h3>
            <p class="text-danger font-size12">Wifi</p>
        </a>
    </div>
    <div class="col-3 item">
        <a href="{{route('gid')}}">
            <h3 class="font-size20"><i class="fas fa-info text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('GID') }}</p>
        </a>
    </div>

    <div class="col-3 item">
        <a href="{{route('bus')}}">
            <h3 class="font-size20"><i class="fas fa-bus text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Bus') }}</p>
        </a>
    </div>

    <div class="col-3 item">
        <a href="{{route('fitnessRoom')}}">
            <h3 class="font-size20"><i class="fas fa-dumbbell text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Fitness room') }}</p>
        </a>
    </div>
    {{--
    <div class="col-3 item">
        <a href="{{route('cafeteria')}}">
            <h3 class="font-size20"><i class="fas fa-coffee text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Cafeteria') }}</p>
        </a>
    </div>
    --}}
    <div class="col-3 item">
        <a href="{{route('pool')}}">
            <h3 class="font-size20"><i class="fas fa-swimmer text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Pool') }}</p>
        </a>
    </div>
    <div class="col-3 item">
        <a href="{{route('studentCheckin')}}">
            <h3 class="font-size20"><i class="fas fa-qrcode text-danger"></i></h3>
            <p class="text-danger font-size12">QR</p>
        </a>
    </div>
    {{--
    <div class="col-3 item">
        <a href="{{route('cinema')}}">
            <h3 class="font-size20"><i class="fas fa-film text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Cinema') }}</p>
        </a>
    </div>

    <div class="col-3 item">
        <a href="{{route('gameLibrary')}}">
            <h3 class="font-size20"><i class="fas fa-gamepad text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Game Library') }}</p>
        </a>
    </div>
    --}}
    <div class="col-3 item">
        <a href="{{route('getCoursesList')}}">
            <h3 class="font-size20"><i class="fab fa-discourse text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Courses') }}</p>
        </a>
    </div>
    {{--
    <div class="col-3 item">
        <a href="{{route('hostel')}}">
            <h3 class="font-size20"><i class="fas fa-hotel text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Housing') }}</p>
        </a>
    </div>
    <div class="col-3 item">
        <a href="{{route('mobilePhones')}}">
            <h3 class="font-size20"><i class="fas fa-mobile-alt text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Mobile phones') }}</p>
        </a>
    </div>
    <div class="col-3 item">
        <a href="{{route('procoffee')}}">
            <h3 class="font-size20"><i class="fas fa-mug-hot text-danger"></i></h3>
            <p class="text-danger font-size12">Procoffee</p>
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
            <h3 class="font-size20"><i class="fas fa-briefcase text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Career') }}</p>
        </a>
    </div>
    <div class="col-3 item">
        <a href="{{route('rentBikes')}}">
            <h3 class="font-size20"><i class="fas fa-biking text-danger"></i></h3>
            <p class="text-danger font-size12">{{ __('Rent bikes') }}</p>
        </a>
    </div>
    --}}

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
            <a href="{{ route('financesPanel') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="far fa-credit-card"></i> {{ __('Finances') }} @if(Lang::locale()=='ru') &nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
        </div>
        <div class="row no-margin margin-b15">
            <a href="{{ route('study') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-university"></i> {{ __('Study') }} @if(Lang::locale()=='ru') &nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
        </div>
        <div class="row no-margin margin-b15">
            <a href="{{ route('deansOffice') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="fas fa-building"></i> {{__('Dean\'s Office')}} @if(Lang::locale()=='ru') &nbsp;&nbsp;&nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp; @endif </a>
        </div>
        @endif
        @if( !\auth()->user() || (\auth()->user() && \auth()->user()->hasRole('listener_course')) )
            <div class="row no-margin margin-b15">
                <a href="{{ route('getCourseCabinet') }}" class="btn btn-danger btn-lg btn-block education-btn"> <i class="far fa-circle"></i> {{ __('Cabinet') }} @if(Lang::locale()=='ru') &nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif </a>
            </div>
        @endif
        @if( !\auth()->user() || (\auth()->user() && \auth()->user()->hasRole('agitator')) )
            <div class="row no-margin margin-b15">
                <a href="{{ route('agitatorProfile') }}" class="btn btn-danger btn-lg btn-block education-btn"> @if(Lang::locale()=='ru') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif @if(Lang::locale()=='kz') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif <i class="fas fa-users-cog"></i> {{ __('Profile agitator') }} </a>
            </div>
        @endif
    </div>


