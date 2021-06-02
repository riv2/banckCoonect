@extends('layouts.app')

@section('title', __('partners.report.title'))

@section('content')

    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4">
        <x-box class="col">
            <x-slot name="title">{{ __('partners.report.sales') }}</x-slot>
            <x-slot name="rightblock">
                <span class="badge bg-success small font-weight-light">@choice('partners.report.fordays', 30)</span>
            </x-slot>
            <div>
                <div class="h1 m-0">@money($stat['orders']['amount_sum'])</div>
                {{-- <div class="stat-percent font-bold text-success float-end"></div> --}}
                {{-- <small></small> --}}
            </div>
        </x-box>
        <x-box class="col">
            <x-slot name="title">
                {{ __('partners.report.' . auth()->user()->pp->pp_target . '.sales') }}
            </x-slot>
            <x-slot name="rightblock">
                <span class="badge bg-danger small font-weight-light">@choice('partners.report.fordays', 30)</span>
            </x-slot>
            <div>
                <div class="h1 m-0">@number($stat['orders']['orders_sale'])</div>
                {{-- <div class="stat-percent font-bold text-success float-end"></div> --}}
                {{-- <small></small> --}}
            </div>
        </x-box>
        <x-box class="col">
            <x-slot name="title">
                {{ __('partners.report.' . auth()->user()->pp->pp_target . '.orders') }}
            </x-slot>
            <x-slot name="rightblock">
                <span class="badge bg-info small font-weight-light">@choice('partners.report.fordays', 30)</span>
            </x-slot>
            <div>
                <div class="h1 m-0">@number($stat['orders']['orders_sum'])</div>
                {{-- <div class="stat-percent font-bold text-success float-end"></div> --}}
                {{-- <small></small> --}}
            </div>
        </x-box>
        <x-box class="col">
            <x-slot name="title">{{ __('partners.report.clicks') }}</x-slot>
            <x-slot name="rightblock">
                <span class="badge bg-primary small font-weight-light">@choice('partners.report.fordays', 30)</span>
            </x-slot>
            <div>
                <div class="h1 m-0">@number($stat['clicks']['clicks'])</div>
                {{-- <div class="stat-percent font-bold text-success float-end"></div> --}}
                {{-- <small></small> --}}
            </div>
        </x-box>
    </div>

    <div class="row">
        <x-box class="col">
            <x-slot name="title">{{ __('partners.report.dynamics-of-confirmed-orders') }}</x-slot>
            <x-slot name="rightblock">
                <div class="col-auto text-end small">
                    <span class="badge bg-primary small font-weight-light">@choice('partners.report.last-days', 7)</span>
                </div>
            </x-slot>

            <div class="row">
                <div class="col-md-9">
                    <canvas id="reportChart" width="100%"
                            data-labels='@json($graph['categories'])'
                            data-new='@json(array_values($graph['series'][1]['data']))'
                            data-newtitle='@json($graph['series'][1]['name'])'
                            data-sale='@json(array_values($graph['series'][2]['data']))'
                            data-saletitle='@json($graph['series'][2]['name'])'
                            data-reject='@json(array_values($graph['series'][3]['data']))'
                            data-rejecttitle='@json($graph['series'][3]['name'])'></canvas>
                </div>
                <div class="col-md-3 my-auto">
                    <ul class="list-unstyled">
                        <li class="my-3">
                            @php
                                $week_count_all = array_sum(array_values($graph['series'][0]['data']));
                                $week_count_new = array_sum(array_values($graph['series'][1]['data']));
                                $week_count_sale = array_sum(array_values($graph['series'][2]['data']));
                                $week_count_reject = array_sum(array_values($graph['series'][3]['data']));
                            @endphp
                            <h2 class="m-0">@number($week_count_all)</h2>
                            <small>{{ __('partners.report.total-orders-for-the-period') }}</small>
                            @if ($week_count_all != 0)
                                <div class="progress">
                                    <div class="progress-bar" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('lists.orderStateList.lead.R.plural.new') }}: {{ $week_count_new }}" style="background-color: #3a8dca; width: {{ ($week_count_new / $week_count_all) * 100 }}%; min-width: 3em;">{{ round($week_count_new / $week_count_all * 100) }}%</div>
                                    <div class="progress-bar" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('lists.orderStateList.lead.R.plural.sale') }}: {{ $week_count_sale }}" style="background-color: #aae0d3; width: {{ ($week_count_sale / $week_count_all) * 100 }}%; min-width: 3em;">{{ round($week_count_sale / $week_count_all * 100) }}%</div>
                                    <div class="progress-bar" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ __('lists.orderStateList.lead.R.plural.reject') }}: {{ $week_count_reject }}" style="background-color: #BBBBBB; width: {{ ($week_count_reject / $week_count_all) * 100 }}%; min-width: 3em;">{{ round($week_count_reject / $week_count_all * 100) }}%</div>
                                </div>
                            @endif
                        </li>
                        {{--
                        <li class="my-3">
                            <h2 class="mt-0">@number(0)</h2>
                            <small>{{ __('partners.report.orders-for-the-last-month') }}</small>
                            <div class="progress">
                                <div class="progress-bar" style="background-color: #3a8dca; width: 60%;"></div>
                            </div>
                        </li>
                         --}}
                    </ul>
                </div>
            </div>
        </x-box>
    </div>

    <x-box>
        <x-slot name="title">{{ __('partners.report.order-list') }}</x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'datetime' => 'format.datetime',
                'offer' => 'format.offer-link',
                'link' => 'format.link-link',
                'readableStatus' => '',
                'amount_currency' => 'html',
            ];
        @endphp
        <x-table :data="$orders" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('partners.report.id') }}</th>
                    <th>{{ __('partners.report.date-and-time-of-order') }}</th>
                    <th>{{ __('partners.report.offer') }}</th>
                    <th>{{ __('partners.report.link') }}</th>
                    <th>{{ __('partners.report.status') }}</th>
                    <th>{{ __('partners.report.order-price') }}</th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('partners.report.no-records-found') }}!
            </x-slot>
        </x-table>
    </x-box>

@endsection
