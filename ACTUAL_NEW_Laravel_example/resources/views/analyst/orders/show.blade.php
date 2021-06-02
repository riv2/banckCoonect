@extends('layouts.app')

@section('title', __('advertiser.orders.show.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.orders.show.order-info') }}</x-slot>
        <x-slot name="rightblock">
{{--            <a href="{{ route(auth()->user()->role . ".orders.edit" , $order->order_id) }}" data-bs-toggle="modal"--}}
{{--               class="btn btn-outline-primary btn-sm">--}}
{{--                {{ __('advertiser.orders.show.edit') }}--}}
{{--            </a>--}}
        </x-slot>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.order-number') }}</div>
                    <div class="col-9">{{ $order->order_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.date') }}</div>
                    <div class="col-9">{{ $order->datetime }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.offer-number') }}</div>
                    <div class="col-9">{{ $order->offer_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.partner-number') }}</div>
                    <div class="col-9">{{ $order->partner_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.category') }}</div>
                    <div class="col-9">{{ $order->category_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.lending-page') }}</div>
                    <div class="col-9">{{ $order->landing_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.link-number') }}</div>
                    <div class="col-9">{{ $order->link_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.click-number') }}</div>
                    <div class="col-9">{{ $order->click_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.web-number') }}</div>
                    <div class="col-9">{{ $order->web_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.pixel') }}</div>
                    <div class="col-9">{{ $order->pixel_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.business-unit') }}</div>
                    <div class="col-9">{{ $order->business_unit_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.registry') }}</div>
                    <div class="col-9">{{ $order->reestr_id }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.status') }}</div>
                    <div class="col-9">{{ $order->readable_status }}</div>
                </div>
            </div>
        </div>
        <x-box>
            <x-slot name="title">
                @if(count($orderProducts) > 0)
                    <div class="col">
                        <div class="col-3 text-dark">{{ __('advertiser.orders.show.products') }}:</div>
                    </div>
                @endif
            </x-slot>
{{--            <x-slot name="rightblock">--}}
{{--                <a href="{{route('advertiser.orders.products.create', $order)}}"--}}
{{--                   class="btn btn-primary btn-sm">--}}
{{--                    <i class="far fa-plus-square"></i> {{ __('Add new product') }}--}}
{{--                </a>--}}
{{--            </x-slot>--}}

            @if(count($orderProducts) > 0)
                @php
                    $format = [
                        'product_name' => '',
                        'price' => '',
                        'quantity' => '',
                        'amount' => '',
                        'readableStatus' => '',
                        'created_at' => 'format.datetime',
                        'updated_at' => 'format.datetime',
                        //'edit_link' => 'html',
                    ];
                @endphp

                <x-table :data="$orderProducts" :format="$format">
                    <x-slot name="thead">
                        <tr>
                            <th>{{ __('advertiser.orders.show.products.title') }}</th>
                            <th>{{ __('advertiser.orders.show.products.price') }}</th>
                            <th>{{ __('advertiser.orders.show.products.quantity') }}</th>
                            <th>{{ __('advertiser.orders.show.products.sum') }}</th>
                            <th>{{ __('advertiser.orders.show.products.status') }}</th>
                            <th>{{ __('advertiser.orders.show.products.created-at') }}</th>
                            <th>{{ __('advertiser.orders.show.products.updated-at') }}</th>
{{--                            <th></th>--}}
                        </tr>
                    </x-slot>
                    <x-slot name="empty">
                        {{ __('advertiser.orders.show.no-records-found') }}
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
    </x-box>


@endsection
