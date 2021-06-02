@extends('layouts.app')

@section('title', __('advertiser.news.show.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">
            <x-slot name="rightblock">
                <a href="{{ route('advertiser.news.send', $item) }}"
                   class="btn btn-primary btn-sm">{{ __('advertiser.news.show.send') }}</a>
            </x-slot>
            {{ $item->news_title }}
        </x-slot>
        {!! $item->news_text !!}
    </x-box>
@endsection
