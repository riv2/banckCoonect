@extends('layouts.app')

@section('title', __('advertiser.news.index.app-title'))

@section('content')
<x-box>
    <x-slot name="title">{{ __('advertiser.news.index.title') }}</x-slot>
    <x-slot name="rightblock">
        <a href="{{ route("advertiser.news.create") }}" data-bs-toggle="modal" class="btn btn-primary btn-sm">
            <i class="far fa-plus-square"></i> {{ __('advertiser.news.index.add-mail-out') }}
        </a>
    </x-slot>
    @php
        $format = [
            'id' => 'number',
            'created_at' => 'format.datetime',
            'view_link' => 'html',
            'news_text_parsed' => 'html',
            'recipients_count' => 'number',
        ];
    @endphp
    <x-table :data="$news" :format="$format">
        <x-slot name="thead">
            <tr>
                <th>{{ __('advertiser.news.index.id') }}</th>
                <th>{{ __('advertiser.news.index.date-time') }}</th>
                <th>{{ __('advertiser.news.index.table-title') }}</th>
                <th>{{ __('advertiser.news.index.mail-out-text') }}</th>
                <th>{{ __('advertiser.news.index.number-of-recipients') }}</th>
            </tr>
        </x-slot>
    </x-table>
</x-box>
@endsection
