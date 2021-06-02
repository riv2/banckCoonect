@extends('layouts.app')

@section('title', __('manager.news.show.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">
            <x-slot name="rightblock">
                <a href="{{ route('manager.news.send', $item) }}"
                   class="btn btn-primary btn-sm">{{ __('manager.news.show.app-title') }}</a>
            </x-slot>
            {{ $item->news_title }}
        </x-slot>
        {{ $item->news_text }}
    </x-box>
@endsection
