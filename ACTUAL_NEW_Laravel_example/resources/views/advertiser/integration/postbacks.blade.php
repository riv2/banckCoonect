@extends('layouts.app')

@section('title', __('advertiser.postbacks.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.postbacks.title') }}</x-slot>
        @php
            $format = [
                'api_id' => 'number',
                'offer_id' => 'number',
                'order_id' => 'string',
                'click_id' => 'string',
                'data_in' => 'format.date',
                'data_out' => 'format.date',
                'status' => 'string',
                'result' => 'number',
                'created_at' => 'format.date',
                'updated_at' => 'format.date',
            ];
        @endphp
        <x-table :format="$format" :data="$postbacks">
            <x-slot name="thead">
                <tr>
                    <th>API ID</th>
                    <th>Offer ID</th>
                    <th>Order ID</th>
                    <th>Click ID</th>
                    <th>data_in</th>
                    <th>data_out</th>
                    <th>{{ __('advertiser.postbacks.status') }}</th>
                    <th>{{ __('advertiser.postbacks.result') }}</th>
                    <th>{{ __('advertiser.postbacks.created_date') }}</th>
                    <th>{{ __('advertiser.postbacks.updated_at') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection

