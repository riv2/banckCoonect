@extends('layouts.app')

@section('title')
    @if(auth()->user()->pp->pp_target == 'products')
        {{ __('advertiser.orders.show.title-order') }}
    @else
        {{ __('advertiser.orders.show.title-lead') }}
    @endif
@endsection

@section('content')
    <x-box>
        <x-slot name="title">
            @if(auth()->user()->pp->pp_target == 'products')
                {{ __('advertiser.orders.show.title-order') }}
            @else
                {{ __('advertiser.orders.show.title-lead') }}
            @endif
        </x-slot>
        <x-slot name="rightblock">
            <a href="{{ route(auth()->user()->role . ".orders.edit" , $order->order_id) }}" data-bs-toggle="modal"
               class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.orders.show.edit') }}
            </a>
        </x-slot>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('advertiser.orders.show.order-number') }}
                        @else
                            {{ __('advertiser.orders.show.lead-number') }}
                        @endif
                    </div>
                    <div class="col-8">{{ $order->order_id }}</div>
                    <div class="col-1">
                        <form method="post" action="{{ route("advertiser.orders.recalc") }}">
                            @csrf
                            <input type="hidden" name="order_id" value = "{{ $order->order_id }}">
                            <button class="btn btn-primary">{{ __('advertiser.orders.index.recalculate') }}</button>
                        </form>
                    </div>
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
                    <div class="col-3 text-dark">
                        @if($order->pp->pp_target == 'lead')
                            {{ __('advertiser.orders.show.gross-amount-lead') }}
                        @else
                            {{ __('advertiser.orders.show.gross-amount-products') }}
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
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.amount') }}</div>
                    <div class="col-9">{{ $order->amount }} @if($order->amount) {{ $order->pp->currency }} @endif</div>
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
    </x-box>
    @if($order->pp->pp_target == 'products')
        <x-box>
        <x-slot name="title">
            @if(count($orderProducts) > 0)
                <div class="col">
                    <div class="col-3 text-dark">{{ __('advertiser.orders.show.products') }}:</div>
                </div>
            @endif
        </x-slot>
        <x-slot name="rightblock">
            <a href="{{route('advertiser.orders.products.create', $order)}}"
               class="btn btn-outline-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.orders.show.products.add') }}
            </a>
        </x-slot>

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
                    'edit_link' => 'html',
                ];
            @endphp

            <x-table :data="$orderProducts" :format="$format">
                <x-slot name="thead">
                    <tr>
                        <th>{{ __('advertiser.orders.show.products.title') }}</th>
                        <th>{{ __('advertiser.orders.show.products.price') }}</th>
                        <th>{{ __('advertiser.orders.show.products.quantity') }}</th>
                        <th>{{ __('advertiser.orders.show.products.fee') }} ({{ $unit }})</th>
                        <th>{{ __('advertiser.orders.show.products.sum') }}</th>
                        <th>{{ __('advertiser.orders.show.products.status') }}</th>
                        <th>{{ __('advertiser.orders.show.products.created-at') }}</th>
                        <th>{{ __('advertiser.orders.show.products.updated-at') }}</th>
                        <th></th>
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
    @endif

@endsection
