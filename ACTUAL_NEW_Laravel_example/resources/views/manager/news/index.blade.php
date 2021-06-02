@extends('layouts.app')

@section('title', __('manager.news.index.app-title'))

@section('content')
<x-box>
    <x-slot name="title">
        {{ __('manager.news.index.app-title') }}
    </x-slot>
    <x-slot name="rightblock">
        <a href="{{ route("manager.news.create") }}" data-bs-toggle="modal" class="btn btn-primary btn-sm">
            <i class="far fa-plus-square"></i> {{ __('manager.news.index.add') }}
        </a>
    </x-slot>
    @php
        $format = [
            'id' => 'number',
            'view_link' => 'html',
            'created_at' => 'format.datetime',
            'news_text_parsed' => 'html',
            'recipients_count' => 'number',
        ];
    @endphp
    <x-table :data="$news" :format="$format">
        <x-slot name="thead">
            <tr>
                <th>ID</th>
                <th>{{ __('manager.news.index.title') }}</th>
                <th>{{ __('manager.news.index.date-time') }}</th>
                <th>{{ __('manager.news.index.body') }}</th>
                <th>{{ __('manager.news.index.quantity') }}</th>
            </tr>
        </x-slot>
    </x-table>
</x-box>
@endsection
