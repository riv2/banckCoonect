@extends('layouts.app')

@section('title', __('advertiser.settings.faq.categories.create.app-title'))

@section('content')
    <x-box>
        <x-slot
            name="title">{{ __('advertiser.settings.faq.categories.create.title') }} {{ $faqCategory->title }}</x-slot>
        <form method="post" action="{{ route('advertiser.settings.faq.category.store', $faqCategory->id) }}"
              enctype="multipart/form-data">
            @csrf
            @method('POST')

            <x-input type="text" name="question"
                     placeholder="{{ __('advertiser.settings.faq.categories.create.question-placeholder') }}" required
                     autofocus>
                {{ __('advertiser.settings.faq.categories.create.question') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.categories.create.question-help') }}</x-slot>
            </x-input>
            <x-input type="textarea" name="answer"
                     placeholder="{{ __('advertiser.settings.faq.categories.create.answer-placeholder') }}"
                     style="min-height:250px">
                {{ __('advertiser.settings.faq.categories.create.answer') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.categories.create.answer-help') }}</x-slot>
            </x-input>
            <x-input type="number" name="faq_category_id" value="{{ $faqCategory->id }}" hidden></x-input>

            <div class="mb-3">
                <button type="submit"
                        class="btn btn-primary">{{ __('advertiser.settings.faq.categories.create.submit') }}</button>
            </div>
        </form>
    </x-box>
@endsection
