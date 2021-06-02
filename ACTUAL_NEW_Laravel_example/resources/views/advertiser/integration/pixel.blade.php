@extends('layouts.app')

@section('title', __('advertiser.integration.pixel.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.integration.pixel.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.servicedeskadv.create") }}?type=technical&subject=%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0%20%D0%BF%D0%B8%D0%BA%D1%81%D0%B5%D0%BB%D1%8F"
               class="btn btn-primary btn-sm">{{ __('advertiser.integration.pixel.right-button') }}</a>
        </x-slot>

        {{ __('advertiser.integration.pixel.two-scripts') }}.<br/>
        {{ __('advertiser.integration.pixel.script') }} <strong>gocpa.cloud
            TRACK</strong> {{ __('advertiser.integration.pixel.set-up-to-all-pages') }}.<br/>
        {{ __('advertiser.integration.pixel.script') }} <strong>gocpa.cloud
            LEAD</strong> {{ __('advertiser.integration.pixel.set-on-checkout-page') }} (checkout-page).<br/>
        {{ __('advertiser.integration.pixel.in-script') }} <strong>gocpa.cloud
            LEAD</strong> {{ __('advertiser.integration.pixel.values-changes') }}
        {{ __('advertiser.integration.pixel.order-data') }}<br/>
        <code>order_id</code> - {{ __('advertiser.integration.pixel.order-id') }}<br/>
        <code>total</code> - {{ __('advertiser.integration.pixel.order-total') }}<br/>

        <br/><br/>
        <h6>{{ __('advertiser.integration.pixel.example') }}:</h6>

        <pre class="prettyprint" style="color:#000000;max-width: 100%; white-space: pre-wrap; font-size: 12px"><span
                style="color:#a65700; ">&lt;</span><span style="color:#800000; font-weight:bold; ">html</span><span
                style="color:#a65700; ">&gt;</span>
    <span style="color:#a65700; ">&lt;</span><span style="color:#800000; font-weight:bold; ">head</span><span
                style="color:#a65700; ">&gt;</span>
        ...
        <span style="color:#696969; ">&lt;!-- Start {{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }} TRACK --&gt;</span>
        <span style="color:#a65700; ">&lt;</span><span style="color:#800000; font-weight:bold; ">script</span><span
                style="color:#a65700; ">&gt;</span>
        <span style="color:#808030; ">!</span><span style="color:#800000; font-weight:bold; ">function</span><span
                style="color:#808030; ">(</span>e<span style="color:#808030; ">,</span>t<span
                style="color:#808030; ">,</span>p<span style="color:#808030; ">,</span>c<span
                style="color:#808030; ">,</span>a<span style="color:#808030; ">,</span>n<span
                style="color:#808030; ">,</span>o<span style="color:#808030; ">)</span><span
                style="color:#800080; ">{</span>e<span style="color:#808030; ">[</span>c<span
                style="color:#808030; ">]</span><span style="color:#808030; ">||</span><span
                style="color:#808030; ">(</span><span style="color:#808030; ">(</span>a<span
                style="color:#808030; ">=</span>e<span style="color:#808030; ">[</span>c<span
                style="color:#808030; ">]</span><span style="color:#808030; ">=</span><span
                style="color:#800000; font-weight:bold; ">function</span><span style="color:#808030; ">(</span><span
                style="color:#808030; ">)</span><span style="color:#800080; ">{</span>a<span
                style="color:#808030; ">.</span>process<span style="color:#800080; ">?</span>a<span
                style="color:#808030; ">.</span>process<span style="color:#808030; ">.</span><span
                style="color:#800000; font-weight:bold; ">apply</span><span style="color:#808030; ">(</span>a<span
                style="color:#808030; ">,</span><span style="color:#797997; ">arguments</span><span
                style="color:#808030; ">)</span><span style="color:#800080; ">:</span>a<span
                style="color:#808030; ">.</span>queue<span style="color:#808030; ">.</span>push<span
                style="color:#808030; ">(</span><span style="color:#797997; ">arguments</span><span
                style="color:#808030; ">)</span><span style="color:#800080; ">}</span><span
                style="color:#808030; ">)</span><span style="color:#808030; ">.</span>queue<span
                style="color:#808030; ">=</span><span style="color:#808030; ">[</span><span
                style="color:#808030; ">]</span><span style="color:#808030; ">,</span>a<span
                style="color:#808030; ">.</span>t<span style="color:#808030; ">=</span><span
                style="color:#808030; ">+</span><span style="color:#800000; font-weight:bold; ">new</span> <span
                style="color:#797997; ">Date</span><span style="color:#808030; ">,</span><span
                style="color:#808030; ">(</span>n<span style="color:#808030; ">=</span>t<span
                style="color:#808030; ">.</span>createElement<span style="color:#808030; ">(</span>p<span
                style="color:#808030; ">)</span><span style="color:#808030; ">)</span><span
                style="color:#808030; ">.</span>async<span style="color:#808030; ">=</span><span
                style="color:#008c00; ">1</span><span style="color:#808030; ">,</span>n<span
                style="color:#808030; ">.</span>src<span style="color:#808030; ">=</span><span
                style="color:#800000; ">"</span><span style="color:#0000e6; ">https://{{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }}/openpixel.min.js?t=</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">+</span><span style="color:#008000; ">864e5</span><span
                style="color:#808030; ">*</span><span style="color:#797997; ">Math</span><span
                style="color:#808030; ">.</span><span style="color:#800000; font-weight:bold; ">ceil</span><span
                style="color:#808030; ">(</span><span style="color:#800000; font-weight:bold; ">new</span> <span
                style="color:#797997; ">Date</span><span style="color:#808030; ">/</span><span style="color:#008000; ">864e5</span><span
                style="color:#808030; ">)</span><span style="color:#808030; ">,</span><span
                style="color:#808030; ">(</span>o<span style="color:#808030; ">=</span>t<span
                style="color:#808030; ">.</span>getElementsByTagName<span style="color:#808030; ">(</span>p<span
                style="color:#808030; ">)</span><span style="color:#808030; ">[</span><span
                style="color:#008c00; ">0</span><span style="color:#808030; ">]</span><span
                style="color:#808030; ">)</span><span style="color:#808030; ">.</span>parentNode<span
                style="color:#808030; ">.</span>insertBefore<span style="color:#808030; ">(</span>n<span
                style="color:#808030; ">,</span>o<span style="color:#808030; ">)</span><span
                style="color:#808030; ">)</span><span style="color:#800080; ">}</span><span
                style="color:#808030; ">(</span>window<span style="color:#808030; ">,</span>document<span
                style="color:#808030; ">,</span><span style="color:#800000; ">"</span><span style="color:#0000e6; ">script</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">,</span><span
                style="color:#800000; ">"</span><span style="color:#0000e6; ">gocpa</span><span style="color:#800000; ">"</span><span
                style="color:#808030; ">)</span><span style="color:#800080; ">;</span>
        gocpa<span style="color:#808030; ">(</span><span style="color:#800000; ">"</span><span style="color:#0000e6; ">init</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">,</span><span
                style="color:#800000; ">"</span><span style="color:#0000e6; ">https://{{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }}/cpapixel.gif</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">)</span><span
                style="color:#800080; ">;</span>
        gocpa<span style="color:#808030; ">(</span><span style="color:#800000; ">"</span><span style="color:#0000e6; ">event</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">,</span><span
                style="color:#800000; ">"</span><span style="color:#0000e6; ">pageload</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">)</span><span
                style="color:#800080; ">;</span>
        <span style="color:#a65700; ">&lt;/</span><span style="color:#800000; font-weight:bold; ">script</span><span
                style="color:#a65700; ">&gt;</span>
        <span style="color:#696969; ">&lt;!-- End {{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }} TRACK  --&gt;</span>
    <span style="color:#a65700; ">&lt;/</span><span style="color:#800000; font-weight:bold; ">head</span><span
                style="color:#a65700; ">&gt;</span>

    <span style="color:#a65700; ">&lt;</span><span style="color:#800000; font-weight:bold; ">body</span><span
                style="color:#a65700; ">&gt;</span>
        ..
        <span style="color:#696969; ">&lt;!-- Start {{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }} LEAD --&gt;</span>
        <span style="color:#a65700; ">&lt;</span><span style="color:#800000; font-weight:bold; ">script</span><span
                style="color:#a65700; ">&gt;</span>
            gocpa<span style="color:#808030; ">(</span><span style="color:#800000; ">"</span><span
                style="color:#0000e6; ">event</span><span style="color:#800000; ">"</span><span style="color:#808030; ">,</span> <span
                style="color:#800000; ">"</span><span style="color:#0000e6; ">purchase</span><span
                style="color:#800000; ">"</span><span style="color:#808030; ">,</span> <span
                style="color:#800080; ">{</span>
                order_id<span style="color:#800080; ">:</span> <span style="color:#800000; ">"</span><span
                style="color:#0000e6; ">42</span><span style="color:#800000; ">"</span><span
                style="color:#808030; ">,</span>
                total<span style="color:#800080; ">:</span> <span style="color:#008c00; ">5000</span><span
                style="color:#808030; ">,</span>
            <span style="color:#800080; ">}</span><span style="color:#808030; ">)</span><span
                style="color:#800080; ">;</span>
        <span style="color:#a65700; ">&lt;/</span><span style="color:#800000; font-weight:bold; ">script</span><span
                style="color:#a65700; ">&gt;</span>
        <span style="color:#696969; ">&lt;!-- End {{ app()->partner_program->prod_domain ?? app()->partner_program->tech_domain }} LEAD  --&gt;</span>
    <span style="color:#a65700; ">&lt;/</span><span style="color:#800000; font-weight:bold; ">body</span><span
                style="color:#a65700; ">&gt;</span>
    <span style="color:#a65700; ">&lt;/</span><span style="color:#800000; font-weight:bold; ">html</span><span
                style="color:#a65700; ">&gt;</span>
    </pre>
    </x-box>
@endsection
