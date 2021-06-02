@extends('layouts.app')

@section('title', __('manager.offers.index.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('manager.offers.index.offers-list') }}</x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'offer_name' => 'string',
                'model' => 'string',
                'datetime' => 'format.date',
                'datetime_end' => 'format.date',
                'fee_string' => 'html',
                'description' => 'html',
                'image' => 'image',
            ];
        @endphp
        <x-table :format="$format" :data="$offers">
            <x-slot name="thead">
                <tr>
                    <th></th>
                    <th>{{ __('manager.offers.index.title') }}</th>
                    <th>{{ __('manager.offers.index.model') }}</th>
                    <th>{{ __('manager.offers.index.date-start') }}</th>
                    <th>{{ __('manager.offers.index.date-end') }}</th>
                    <th>{{ __('manager.offers.index.fee') }}</th>
                    <th>{{ __('manager.offers.index.desc') }}</th>
                    <th>{{ __('manager.offers.index.img') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
