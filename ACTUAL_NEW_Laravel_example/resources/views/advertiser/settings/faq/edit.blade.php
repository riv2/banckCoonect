@extends('layouts.app')

@section('title', __('advertiser.settings.faq.edit.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.faq.edit.title') }} {{ $faqCategory->title }}</x-slot>
        <form method="post" action="{{ route('advertiser.settings.faq.update', $faqCategory->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-input type="text" name="title"
                     placeholder="{{ __('advertiser.settings.faq.edit.input-title-placeholder') }}"
                     value="{{$faqCategory->title}}" required autofocus>
                {{ __('advertiser.settings.faq.edit.input-title') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.edit.help') }}</x-slot>
            </x-input>
            <x-input type="number" name="id" value="{{ $faqCategory->id }}" hidden></x-input>


            <div class="mb-3">
                <button type="submit" class="btn btn-primary">{{ __('advertiser.settings.faq.edit.submit') }}</button>
            </div>
        </form>
    </x-box>
@endsection
