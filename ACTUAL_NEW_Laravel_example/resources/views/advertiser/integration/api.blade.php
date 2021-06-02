@extends('layouts.app')

@section('title', __('advertiser.integration.api.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.integration.api.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.servicedeskadv.create") }}?type=technical&subject=%D0%98%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F%20%D0%BF%D0%BE%20API"
               class="btn btn-primary btn-sm">{{ __('advertiser.integration.api.right-button') }}</a>
        </x-slot>
        <br/>
        <h6>{{ __('advertiser.integration.api.title-1') }}</h6>
        <p>
            {{ __('advertiser.integration.api.p-1') }}.
        </p>
        <ul>
            <li>{{ __('advertiser.integration.api.li-0-0') }}.</li>
            <li>{{ __('advertiser.integration.api.li-0-1') }}.</li>
            <li>{{ __('advertiser.integration.api.li-0-2') }}.</li>
            <li>{{ __('advertiser.integration.api.li-0-3') }}.</li>
        </ul>
        <br/>
        <h6>{{ __('advertiser.integration.api.title-2') }}</h6>
        <ol>
            <li>{{ __('advertiser.integration.api.li-1-0') }}.</li>
            <li>{{ __('advertiser.integration.api.li-1-1') }}.</li>
            <li>{{ __('advertiser.integration.api.li-1-2') }}.</li>
            <li>{{ __('advertiser.integration.api.li-1-3') }}.</li>
            <li>{{ __('advertiser.integration.api.li-1-3') }}.</li>
        </ol>
        <br/>
        <h6>{{ __('advertiser.integration.api.title-3') }}</h6>
        <p>
            {{ __('advertiser.integration.api.p-2') }}.
        </p>
        <p>
            {{ __('advertiser.integration.api.link-example') }}:<br><a
                href="https://domain.com/?utm_term=598c4dbd-c904-4164-951c-e57c40fdfb80&utm_content=1&utm_medium=cpa&utm_source=partners&utm_campaign=1106">https://domain.com/?utm_term=598c4dbd-c904-4164-951c-e57c40fdfb80&utm_content=1&utm_medium=cpa&utm_source=partners&utm_campaign=1106</a>
        </p>
        <p>
            {{ __('advertiser.integration.api.p-3') }}
        </p>
        <p>
            {{ __('advertiser.integration.api.p-4') }}
        </p>
        <p>
            {{ __('advertiser.integration.api.p-5') }}
        </p>
        <p>
            {{ __('advertiser.integration.api.p-6') }}
        </p>
        <br/>
        <h6>{{ __('advertiser.integration.api.title-4') }}</h6>
        <pre class="prettyprint">
if (isset($_GET['utm_source']) && $_GET['utm_source']== 'partners') {
    setcookie('partners_url', $_SERVER['REQUEST_URI'], time() + 60 * 60 * 24 * 90, '/');
}
</pre>

        <br/>
        <h6>{{ __('advertiser.integration.api.title-5') }}</h6>
        <p>
            {{ __('advertiser.integration.api.entrance-point') }}: <a
                href="https://@php echo $_SERVER['HTTP_HOST'] @endphp/adv_api/{{auth()->user()->pp->id}}/">https://@php echo $_SERVER['HTTP_HOST'] @endphp/adv_api/{{auth()->user()->pp->id}}</a><br/>
            {{ __('advertiser.integration.api.request-format') }}: GET<br/>
            {{ __('advertiser.integration.api.request-params') }}:
        </p>
        <table class="table-bordered">
            <tr>
                <td><strong>{{ __('advertiser.integration.api.parameter') }}</strong></td>
                <td><strong>{{ __('advertiser.integration.api.description') }}</strong></td>
                <td><strong>{{ __('advertiser.integration.api.format') }}</strong></td>
            </tr>
            <tr>
                <td>order_id</td>
                <td>{{ __('advertiser.integration.api.order-id') }}</td>
                <td>string</td>
            </tr>
            <tr>
                <td>url</td>
                <td>{{ __('advertiser.integration.api.url') }}</td>
                <td>string</td>
            </tr>
            <tr>
                <td>offer_id</td>
                <td>{{ __('advertiser.integration.api.offer-id') }}</td>
                <td>int</td>
            </tr>
            <tr>
                <td>gross_amount</td>
                <td>{{ __('advertiser.integration.api.gross_amount') }}</td>
                <td>float</td>
            </tr>
            <tr>
                <td>status</td>
                <td>
                    {{ __('advertiser.integration.api.order-status') }}:<br/>
                    {{ __('advertiser.integration.api.status-new') }}<br/>
                    {{ __('advertiser.integration.api.status-sale') }}<br/>
                    {{ __('advertiser.integration.api.status-reject') }}
                </td>
                <td>string</td>
            </tr>
            <tr>
                <td>hash</td>
                <td>{{ __('advertiser.integration.api.secret-key') }}</td>
                <td>string</td>
            </tr>
        </table>
        <br/><br/>
        <h6>{{ __('advertiser.integration.api.title-6') }}:</h6>
        <p>
            <a href="https://@php echo $_SERVER['HTTP_HOST'] @endphp/adv_api/{{auth()->user()->pp->id}}/?order_id=123456&url=https%3A%2F%2Fdomain.com%2F%3Futm_term%3D598c4dbd-c904-4164-951c-e57c40fdfb80%26utm_content%3D1%26utm_medium%3Dcpa%26utm_source%3Dpartners%26utm_campaign%3D1106&offer_id=1&status=new&hash={{auth()->user()->hash_name}}">https://@php echo $_SERVER['HTTP_HOST'] @endphp/adv_api/{{auth()->user()->pp->id}}/?order_id=123456&url=https%3A%2F%2Fdomain.com%2F%3Futm_term%3D598c4dbd-c904-4164-951c-e57c40fdfb80%26utm_content%3D1%26utm_medium%3Dcpa%26utm_source%3Dpartners%26utm_campaign%3D1106&offer_id=1&status=new&hash={{auth()->user()->hash_name}}</a>
        </p>
        <p>
            {{ __('advertiser.integration.api.p-7') }}.
        </p>

    </x-box>
@endsection


