@extends('layouts.app')

@section('title', __('menu.advertiser.settings.faq.index'))

@section('content')
    <x-box>
        <x-slot
            name="title">{{ __('advertiser.settings.faq.categories.index.title') }} {{ $faqCategory->title }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('advertiser.settings.faq.index') }}"
               class="btn btn-primary btn-sm">{{ __('advertiser.settings.faq.categories.index.link-to-index') }}</a>
            <a href="{{ route('advertiser.settings.faq.category.create', $faqCategory->id) }}"
               class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.settings.faq.categories.index.add-question') }}
            </a>
        </x-slot>

        @php
            $format = [
                'question' => 'string',
                'answer' => 'string',
                'created_at' => 'format.date',
                'updated_at' => 'format.date',
                'edit_button' => 'html',
                'destroy_button' => 'html',
            ];
        @endphp
        <x-table :format="$format" :data="$collection">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.settings.faq.categories.index.question') }}</th>
                    <th>{{ __('advertiser.settings.faq.categories.index.answer') }}</th>
                    <th>{{ __('advertiser.settings.faq.categories.index.created_at') }}</th>
                    <th>{{ __('advertiser.settings.faq.categories.index.updated_at') }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
