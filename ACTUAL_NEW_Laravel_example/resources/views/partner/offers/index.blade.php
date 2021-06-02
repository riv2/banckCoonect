@extends('layouts.app')

@section('title', __('partners.offers.index.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('partners.offers.index.offers_list') }}</x-slot>
        @php
            $format = [
                'id' => '',
                'view_link' => 'html',
                'landing_link' => 'html',
                'created_at' => 'format.date',
                'updated_at' => 'format.date',
                'fee_string' => 'html',
                'description' => 'html',
                'image' => 'image',
            ];
        @endphp
        <x-table :format="$format" :data="$collection">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('partners.offers.index.id') }}</th>
                    <th>{{ __('partners.offers.index.name') }}</th>
                    <th>{{ __('partners.offers.index.link') }}</th>
                    <th>{{ __('partners.offers.index.created_at') }}</th>
                    <th>{{ __('partners.offers.index.updated_at') }}</th>
                    <th>{{ __('partners.offers.index.fee') }}</th>
                    <th>{{ __('partners.offers.index.description') }}</th>
                    <th>{{ __('partners.offers.index.image') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
