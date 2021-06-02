@extends('layouts.app')

@section('title', __('advertiser.banned_links.index.app-title'))

@section('content')
    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_links.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.banned-links.create") }}" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.banned_links.index.add-banned-link') }}
            </a>
        </x-slot>
        @php
            $format = [
                'created_at' => 'format.date',
                'view_link' => 'html',
                'web_id' => '',
                'partner' => '',
                'comment' => 'html.banned-link',
                'evidence' => 'html.banned-link',
                'date_start' => 'format.datetime',
                'date_end' => 'format.datetime',
            ];
        @endphp
        <x-table :data="$bannedLinks" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.banned_links.index.date-time') }}</th>
                    <th>{{ __('advertiser.banned_links.index.link-id') }}</th>
                    <th>{{ __('advertiser.banned_links.index.web-id') }}</th>
                    <th>{{ __('advertiser.banned_links.index.partner') }}</th>
                    <th>{{ __('advertiser.banned_links.index.comment') }}</th>
                    <th>{{ __('advertiser.banned_links.index.evidence') }}</th>
                    <th>{{ __('advertiser.banned_links.index.banned-start') }}</th>
                    <th>{{ __('advertiser.banned_links.index.banned-end') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
