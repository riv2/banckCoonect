@extends('layouts.app')

@section('title', __('advertiser.offers.index.title.offers'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.offers.index.title.offers.list') }}</x-slot>

        @php
            $format = [
                'view_link' => 'html',
                'created_at' => 'format.date',
                'updated_at' => 'format.date',
                'fee_advert_string' => 'html',
                'description' => 'html',
                'image' => 'image',
            ];
        @endphp
        <x-table :format="$format" :data="$collection">
            <x-slot name="thead">
                <tr>
                    <th>{{ __('advertiser.offers.index.name') }}</th>
                    <th>{{ __('advertiser.offers.index.create_date') }}</th>
                    <th>{{ __('advertiser.offers.index.updated_date') }}</th>
                    <th>{{ __('advertiser.offers.index.fee') }}</th>
                    <th>{{ __('advertiser.offers.index.desc') }}</th>
                    <th>{{ __('advertiser.offers.index.img') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
