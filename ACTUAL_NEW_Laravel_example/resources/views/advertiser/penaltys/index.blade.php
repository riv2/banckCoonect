@extends('layouts.app')

@section('title', __('advertiser.penaltys.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.penaltys.index.order-list') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.penaltys.create") }}" data-bs-toggle="modal" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.penaltys.index.add') }}
            </a>
            <a href="{{ route("advertiser.orders.export", request()->all()) }}" class="btn btn-outline-primary btn-sm">
                <i class="far fa-file-excel"></i> {{ __('advertiser.orders.index.export') }}
            </a>
        </x-slot>
        @php
            $format = [
                'penalty_view_link' => 'html',
                'datetime' => 'format.datetime',
                'partner' => 'format.user-link',
                'type' => '',
                'gross_amount' => 'format.money',
                'comment' => '',
                'edit_link' => 'html',
            ];
        @endphp
        <x-table :data="$orders" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.penaltys.index.id') }}</th>
                    <th>{{ __('advertiser.penaltys.index.order-date-time') }}</th>
                    <th>{{ __('advertiser.penaltys.index.partner') }}</th>
                    <th>{{ __('advertiser.penaltys.index.type') }}</th>
                    <th>{{ __('advertiser.penaltys.index.sum') }}</th>
                    <th>{{ __('advertiser.penaltys.index.comment') }}</th>
                    <th></th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('advertiser.orders.index.no-record-found') }}
            </x-slot>
        </x-table>
    </x-box>
@endsection

