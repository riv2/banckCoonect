@extends('layouts.app')

@section('title', __('advertiser.settings.faq.categories.edit.app-title'))

@section('content')
    <x-box>
        <x-slot
            name="title">{{ __('advertiser.settings.faq.categories.edit.title') }} {{ $faqCategory->title }}</x-slot>
        <form method="post" action="{{ route('advertiser.settings.faq.category.update',
        ['faq' => $faqCategory->id, 'category' => $faq->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-input type="text" name="question"
                     placeholder="{{ __('advertiser.settings.faq.categories.edit.question-placeholder') }}"
                     value="{{ $faq->question }}" required autofocus>
                {{ __('advertiser.settings.faq.categories.edit.question') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.categories.edit.question-help') }}</x-slot>
            </x-input>
            <x-input type="textarea" name="answer"
                     placeholder="{{ __('advertiser.settings.faq.categories.edit.answer-placeholder') }}"
                     value="{!! $faq->answer ?? '' !!}" style="min-height:250px">
                {{ __('advertiser.settings.faq.categories.edit.answer') }}
                <x-slot name="help">{{ __('advertiser.settings.faq.categories.edit.answer-help') }}</x-slot>
            </x-input>

            <div class="mb-3">
                <button type="submit"
                        class="btn btn-primary">{{ __('advertiser.settings.faq.categories.edit.submit') }}</button>
            </div>
        </form>
    </x-box>
@endsection
