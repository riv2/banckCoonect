@extends('layouts.app')

@section('title', __('advertiser.orders.index.app.title'))

@section('content')

    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>



    <x-box>
        <x-slot name="title">{{ __('advertiser.orders.index.order-list') }}</x-slot>

        <x-slot name="rightblock">
            <a href="{{ route("analyst.orders.export", request()->all()) }}" class="btn btn-outline-primary btn-sm">
                <i class="far fa-file-excel"></i> {{ __('analyst.orders.index.export') }}
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
            ];
        @endphp
        <x-table :data="$orders" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.orders.index.id') }}</th>
                    <th>{{ __('advertiser.orders.index.order-date-time') }}</th>
                    <th>{{ __('advertiser.orders.index.offer') }}</th>
                    <th>{{ __('advertiser.orders.index.partner') }}</th>
                    <th>{{ __('advertiser.orders.index.link') }}</th>
                    <th>{{ __('advertiser.orders.index.status') }}</th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('advertiser.orders.index.no-record-found') }}
            </x-slot>
        </x-table>
    </x-box>

@endsection
