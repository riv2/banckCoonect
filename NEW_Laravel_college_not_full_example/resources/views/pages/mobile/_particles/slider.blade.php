
<div class="uk-position-relative uk-visible-toggle uk-light margin-b10" tabindex="-1" uk-slider="autoplay: true">

    <ul class="uk-slider-items uk-grid">

        <li class="uk-width-1">
            <div class="uk-panel">
            @if(\App\Services\Auth::user()->hasRole('client'))
                <a href="{{route('studentPayToBalance')}}">
                    <img src="{{ URL::asset('/lte_assets/img/pay_click_banner.jpg') }}" />
                </a>
            @else
                <img src="{{ URL::asset('/lte_assets/img/pay_banner.jpg') }}" />
            @endif
            </div>
        </li>

        <li class="uk-width-1">
            <div class="uk-panel">
                <a href="{{route('agitatorRegisterIndex')}}">
                    <img src="{{ URL::asset('/lte_assets/img/agitator_banner_' .Lang::locale(). '.jpg') }}" />
                </a>
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <a href="{{route('wifi')}}">
                    <img src="{{ URL::asset('lte_assets/slider/wifi_' .Lang::locale(). '.jpg') }}" />
                </a>
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <a href="{{route('callBack')}}">
                    <img src="{{ URL::asset('lte_assets/slider/call_center.png') }}" />
                </a>
            </div>
        </li>

        {{--
        <li class="uk-width-1">
            <div class="uk-panel">
                <img src="{{ URL::asset('lte_assets/slider/study.jpg') }}" />
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <img src="{{ URL::asset('lte_assets/slider/pool.jpg') }}" />
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <img src="{{ URL::asset('lte_assets/slider/painball.jpg') }}" />
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <img src="{{ URL::asset('lte_assets/slider/cafe.jpg') }}" />
            </div>
        </li>
        <li class="uk-width-1">
            <div class="uk-panel">
                <img src="{{ URL::asset('lte_assets/slider/bus.jpg') }}" />
            </div>
        </li>
        --}}

    </ul>

</div>
