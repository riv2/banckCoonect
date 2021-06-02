@extends('layouts.app')

@section('title', __('advertiser.banned_frauds.index.app-title'))

@section('content')
    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_frauds.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.banned-frauds.create") }}" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.banned_frauds.index.add-banned-fraud') }}
            </a>
            <a href="{{ route("advertiser.banned-frauds.export", request()->all()) }}" class="btn btn-outline-primary btn-sm" title="Export to Excel">
                <i class="far fa-file-excel"></i> {{ __('advertiser.banned_frauds.index.export') }}
            </a>
        </x-slot>
        @php
            $format = [
                'created_at' => 'format.datetime',
                'view_link' => 'html',
                'offer' => 'html',
                'partner' => 'banned_fraud.partner',
                'comment' => '',
                'evidence' => '',
            ];
        @endphp
        <x-table :data="$bannedFrauds" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.banned_frauds.index.date-time') }}</th>
                    <th>{{ __('advertiser.banned_frauds.index.order-id') }}</th>
                    <th>{{ __('advertiser.banned_frauds.index.offer') }}</th>
                    <th>{{ __('advertiser.banned_frauds.index.partner') }}</th>
                    <th>{{ __('advertiser.banned_frauds.index.comment') }}</th>
                    <th>{{ __('advertiser.banned_frauds.index.evidence') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
