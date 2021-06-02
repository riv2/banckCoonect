@extends('layouts.app')

@section('title', __('advertiser.settings.faq.show.app-title') . ' ' . $faqCategory->title)

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.faq.show.title') }} {{ $faqCategory->title }}</x-slot>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.settings.faq.show.created_at') }}</div>
                    <div class="col-9">{{ $faqCategory->created_at }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.settings.faq.show.updated_at') }}</div>
                    <div class="col-9">{{ $faqCategory->updated_at }}</div>
                </div>
            </div>
            <div class="list-group-item">
                <div class="row">
                    <div class="col-3 text-dark">{{ __('advertiser.settings.faq.show.position') }}</div>
                    <div class="col-9">{{ $faqCategory->position }}</div>
                </div>
            </div>
        </div>
    </x-box>
@endsection
