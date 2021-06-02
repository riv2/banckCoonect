@extends('layouts.app')

@section('title')
    @if(auth()->user()->pp->pp_target == 'products')
        {{ __('partners.orders.show.title-order') }}
    @else
        {{ __('partners.orders.show.title-lead') }}
    @endif
@endsection

@section('content')

    <x-box>
        <x-slot name="title">
            @if(auth()->user()->pp->pp_target == 'products')
                {{ __('partners.orders.show.title-order') }}
            @else
                {{ __('partners.orders.show.title-lead') }}
            @endif
        </x-slot>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">№
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('partners.orders.show.order') }}
                        @else
                            {{ __('partners.orders.show.lead') }}
                        @endif
                    </div>
                    <div class="col-9">{{ $order->order_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.date') }}</div>
                    <div class="col-9">{{ $order->datetime }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">№ {{ __('partners.orders.show.offer') }}</div>
                    <div class="col-9">{{ $order->offer_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.partner') }} №</div>
                    <div class="col-9">{{ $order->partner_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.category') }}</div>
                    <div class="col-9">{{ $order->category_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.landing') }}</div>
                    <div class="col-9">{{ $order->landing_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.link') }} №</div>
                    <div class="col-9">{{ $order->link_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.click') }} №</div>
                    <div class="col-9">{{ $order->click_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.web') }} №</div>
                    <div class="col-9">{{ $order->web_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.pixel') }}</div>
                    <div class="col-9">{{ $order->pixel_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.business-unit') }}</div>
                    <div class="col-9">{{ $order->business_unit_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">
                        @if($order->pp->pp_target == 'lead')
                            {{ __('partners.orders.show.gross-amount-lead') }}
                        @else
                            {{ __('partners.orders.show.gross-amount-products') }}
                        @endif
                    </div>
                    <div class="col-9">{{ $order->gross_amount }} @if($order->gross_amount) {{ $order->pp->currency }} @endif</div>
                </div>
            </div>
            @if($order->pp->pp_target == 'lead')
                <div class="list-group-item">
                    <div class="row">
                        <div class="col-3 text-dark">{{ __('advertiser.orders.show.fee') }} ({{ $unit }})</div>
                        <div class="col-9">{{ $order->fee }} @if($order->fee) {{ $unit }} @endif</div>
                    </div>
                </div>
            @endif
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.amount') }}</div>
                    <div class="col-9">{{ $order->amount }} @if($order->amount) {{ $order->pp->currency }} @endif </div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.registry') }}</div>
                    <div class="col-9">{{ $order->reestr_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('partners.orders.show.status') }}</div>
                    <div class="col-9">{{ \App\Lists\OrderStateList::getList()[$order->status] }}</div>
                </div>
            </div>
            @if(count($orderProducts) > 0)
                <div class="list-group-item">
                    <div class="row">
                        <div class="col-3 text-dark">{{ __('partners.orders.show.products') }}:</div>
                    </div>
                </div>
            @endif
        </div>
        @if(count($orderProducts) > 0)
            @php
                $format = [
                    'product_name' => '',
                    'price' => '',
                    'quantity' => '',
                    'fee_string' => 'html',
                    'amount' => '',
                    'readableStatus' => '',
                    'created_at' => 'format.datetime',
                    'updated_at' => 'format.datetime',
                ];
            @endphp
            <x-table :data="$orderProducts" :format="$format">
                <x-slot name="thead">
                    <tr>
                        <th>{{ __('partners.orders.show.products.name') }}</th>
                        <th>{{ __('partners.orders.show.products.price') }}</th>
                        <th>{{ __('partners.orders.show.products.quantity') }}</th>
                        <th>{{ __('partners.orders.show.products.fee') }} ({{ $unit }})</th>
                        <th>{{ __('partners.orders.show.products.sum') }}</th>
                        <th>{{ __('partners.orders.show.products.status') }}</th>
                        <th>{{ __('partners.orders.show.products.crated-date') }}</th>
                        <th>{{ __('partners.orders.show.products.updated-date') }}</th>
                        <th></th>
                    </tr>
                </x-slot>
                <x-slot name="empty">
                    {{ __('partners.orders.show.products.no-records-found') }}
                </x-slot>
            </x-table>
        @endif
        <br>
        @if(count($notify) > 0)
            <h6>{{ __('partners.orders.show.postbacks') }}</h6>
            @php
                $format = [
                    'sent_datetime' => 'format.datetime',
                    'sent_url' => '',
                    'status' => '',
                    'responce_httpcode' => '',
                    'responce_body' => '',
                ];
            @endphp
            <x-table :data="$notify" :format="$format">
                <x-slot name="empty">
                    {{ __('partners.orders.show.products.no-records-found') }}
                </x-slot>
            </x-table>
        @endif
    </x-box>

@endsection
