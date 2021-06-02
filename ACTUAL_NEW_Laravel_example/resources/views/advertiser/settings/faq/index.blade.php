@extends('layouts.app')

@section('title', __('menu.advertiser.settings.faq.index'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.faq.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.settings.faq.create") }}" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.settings.faq.index.add-section') }}</a>
        </x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'created_at' => 'format.date',
                'updated_at' => 'format.date',
                'edit_button' => 'html',
                'destroy_button' => 'html',
            ];
        @endphp
        <x-table :format="$format" :data="$collection">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.settings.faq.index.name') }}</th>
                    <th>{{ __('advertiser.settings.faq.index.created-at') }}</th>
                    <th>{{ __('advertiser.settings.faq.index.updated-at') }}</th>
                    <th></th>
                    <th></th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
