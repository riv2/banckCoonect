@extends('layouts.app')

@section('title', __('partners.banned_links.index.app-title'))

@section('content')
    <x-box>
        @includeWhen(isset($filter_fields), 'widgets.filter_fields.form')
    </x-box>
    <x-box>
        <x-slot name="title">{{ __('partners.banned_links.index.title') }}</x-slot>
        @php
            $format = [
                'view_details' => 'html',
                'web_id' => '',
                'comment' => 'html.banned-link',
                'evidence' => 'html.banned-link',
                'date_start' => 'format.datetime',
                'date_end' => 'format.datetime',
            ];
        @endphp
        <x-table :data="$bannedLinks" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('partners.banned_links.index.link-id') }}</th>
                    <th>{{ __('partners.banned_links.index.web-id') }}</th>
                    <th>{{ __('partners.banned_links.index.comment') }}</th>
                    <th>{{ __('partners.banned_links.index.evidence') }}</th>
                    <th>{{ __('partners.banned_links.index.banned-start') }}</th>
                    <th>{{ __('partners.banned_links.index.banned-end') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
