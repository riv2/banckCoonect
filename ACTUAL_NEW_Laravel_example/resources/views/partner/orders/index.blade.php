@extends('layouts.app')

@section('title')
    @if(auth()->user()->pp->pp_target == 'products')
        {{ __('partners.orders.index.orders') }}
    @else
        {{ __('partners.orders.index.leads') }}
    @endif
@endsection

@section('content')

    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>

    <x-box>
        <x-slot name="title">
            @if(auth()->user()->pp->pp_target == 'products')
                {{ __('partners.orders.index.orders-list') }}
            @else
                {{ __('partners.orders.index.leads-list') }}
            @endif
        </x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('partner.orders.export', request()->all()) }}" class="btn btn-outline-primary btn-sm">
                <i class="far fa-file-excel"></i> {{ __('advertiser.orders.index.export') }}
            </a>
        </x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'datetime' => 'format.datetime',
                'offer' => 'format.offer-link',
                'link' => 'format.link-link',
                'readableStatus' => '',
                'gross_amount' => 'format.money',
            ];
        @endphp
        <x-table :data="$orders" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>ID</th>
                    <th>
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('partners.orders.index.order-datetime') }}
                        @else
                            {{ __('partners.orders.index.lead-datetime') }}
                        @endif
                    </th>
                    <th>{{ __('partners.orders.index.offer') }}</th>
                    <th>{{ __('partners.orders.index.link') }}</th>
                    <th>{{ __('partners.orders.index.status') }}</th>
                    <th>
                        @if(auth()->user()->pp->pp_target == 'products')
                            {{ __('partners.orders.index.order-sum') }}
                        @else
                            {{ __('partners.orders.index.lead-sum') }}
                        @endif
                    </th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('partners.orders.index.no-records-found') }}
            </x-slot>
        </x-table>
    </x-box>

@endsection
