@extends('layouts.app')

@section('title', __('partners.news.app.title'))

@section('content')
<x-box>
    <x-slot name="title">{{ __('partners.news.app.title') }}</x-slot>
    @php
        $format = [
            'created_at' => 'format.datetime',
            'view_link' => 'html',
            'news_text_parsed' => 'html',
        ];
    @endphp
    <x-table :data="$news" :format="$format">
        <x-slot name="thead">
            <tr>
                <th>{{ __('partners.news.date-time') }}</th>
                <th>{{ __('partners.news.title') }}</th>
                <th>{{ __('partners.news.text') }}</th>
            </tr>
        </x-slot>
    </x-table>
</x-box>
@endsection
