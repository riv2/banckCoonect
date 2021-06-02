@extends('layouts.app')

@section('title', __('advertiser.partners.index.app.title'))

@section('content')
    @include('helpers.filter')

    <x-box>
        <x-slot name="title">{{ __('advertiser.partners.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('advertiser.partners.create') }}" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square"></i> {{ __('advertiser.partners.index.add-partner') }}
            </a>
        </x-slot>

        @php
            $format = [
                'id' => 'format.string',
                'view_link' => 'html',
                'created_at' => 'format.datetime',
                'name' => 'format.string',
                'impersonate_link_button' => 'html',
            ];
        @endphp
        <x-table :data="$partners" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.partners.index.id') }}</th>
                    <th>{{ __('advertiser.partners.index.email') }}</th>
                    <th>{{ __('advertiser.partners.index.registration-date') }}</th>
                    <th>{{ __('advertiser.partners.index.name') }}</th>
                    <th>{{ __('advertiser.partners.index.authorise') }}</th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                {{ __('advertiser.partners.index.no-records-found') }}
            </x-slot>
        </x-table>
    </x-box>
@endsection
