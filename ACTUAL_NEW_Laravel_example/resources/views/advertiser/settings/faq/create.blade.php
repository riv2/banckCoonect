@extends('layouts.app')

@section('title', __('advertiser.settings.faq.create.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.faq.create.title') }}</x-slot>
        <form method="post" action="{{ route('advertiser.settings.faq.store') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')
            <x-input type="text" name="title" placeholder="{{ __('advertiser.settings.faq.create.input-title-placeholder') }}"
                     required autofocus>
                {{ __('advertiser.settings.faq.create.input-title') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.create.help') }}</x-slot>
            </x-input>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">{{ __('advertiser.settings.faq.create.submit') }}</button>
            </div>
        </form>
    </x-box>
@endsection
