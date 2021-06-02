@extends('layouts.app')

@section('title')
    @if(auth()->user()->pp->pp_target == 'products')
        {{ __('advertiser.orders.index.app.title.orders') }}
    @else
        {{ __('advertiser.orders.index.app.title.leads') }}
    @endif
@endsection

@section('content')

    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>

    <x-box>
        <x-slot name="title">{{ __('advertiser.orders.index.title') }}</x-slot>
        <form method="post" action="{{ route("advertiser.orders.recalc") }}">
            @csrf
            <div class="row">
                <div class="col col-xs-3">
                    <div class="form-label-group">
                        <label class="form-label">{{ __('advertiser.orders.index.date-from') }}</label>
                        <input type="date" name="date_from" class="form-control">
                    </div>
                </div>
                <div class="col col-xs-3">
                    <div class="form-label-group">
                        <label class="form-label">{{ __('advertiser.orders.index.date-to') }}</label>
                        <input type="date" name="date_to" class="form-control">
                    </div>
                </div>
                <div class="col col-xs-2">
                    <div class="form-label-group">
                        <label class="form-label">{{ __('advertiser.orders.index.partner-id') }}</label>
                        <input type="text" name="partner_id" class="form-control">
                    </div>
                </div>
                <div class="col col-xs-2">
                    <div class="form-label-group">
                        <label class="form-label">{{ __('advertiser.orders.index.order-id') }}</label>
                        <input type="text" name="order_id" class="form-control">
                    </div>
                </div>
                <div class="col col-xs-2">
                    <button class="btn btn-primary">{{ __('advertiser.orders.index.recalculate') }}</button>
                </div>

            </div>

        </form>
    </x-box>
    <x-box>
        <x-slot name="title">
            @if(auth()->user()->pp->pp_target == 'products')
                {{ __('advertiser.orders.index.order-list.orders') }}
            @else
                {{ __('advertiser.orders.index.order-list.leads') }}
            @endif
        </x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.create") }}" data-bs-toggle="modal" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.orders.index.add') }}
            </a>
            <a href="{{ route("advertiser.orders.export", request()->all()) }}" class="btn btn-outline-primary btn-sm">
                <i class="far fa-file-excel"></i> {{ __('advertiser.orders.index.export') }}
            </a>
        </x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'datetime' => 'format.datetime',
                'offer' => 'format.offer-link',
                'partner' => 'format.user-link',
                'link' => 'format.link-link',
                'readableStatus' => '',
                'gross_amount' => 'format.money',
            ];
        @endphp
        <x-table :data="$orders" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.orders.index.id') }}</th>
                    <th>
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('advertiser.orders.index.order-date-time') }}
                        @else
                            {{ __('advertiser.orders.index.lead-date-time') }}
                        @endif
                    </th>
                    <th>{{ __('advertiser.orders.index.offer') }}</th>
                    <th>{{ __('advertiser.orders.index.partner') }}</th>
                    <th>{{ __('advertiser.orders.index.link') }}</th>
                    <th>{{ __('advertiser.orders.index.status') }}</th>
                    <th>
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('advertiser.orders.index.order-sum') }}
                        @else
                            {{ __('advertiser.orders.index.lead-sum') }}
                        @endif
                    </th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('advertiser.orders.index.no-record-found') }}
            </x-slot>
        </x-table>
    </x-box>

@endsection
