@extends('layouts.app')

@section('title', __('advertiser.servicedeskadv.index.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.servicedeskadv.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.servicedeskadv.create") }}" class="btn btn-primary btn-sm">
                <i class="far fa-plus-square">
                </i> {{ __('advertiser.servicedeskadv.index.add-ticket') }}</a>
        </x-slot>
        @php
            $format = [
                'id' => 'number',
                'view_link' => 'html',
                'created_at' => 'format.date',
                'deadline_at' => 'format.date',
                'type_text' => 'string',
                'status_text' => 'string',
            ];
        @endphp
        <x-table :format="$format" :data="$tasks">
            <x-slot name="thead">
                <tr>
                    <th>ID</th>
                    <th>{{ __('advertiser.servicedeskadv.index.theme') }}</th>
                    <th>{{ __('advertiser.servicedeskadv.index.created_at') }}</th>
                    <th>{{ __('advertiser.servicedeskadv.index.deadline') }}</th>
                    <th>{{ __('advertiser.servicedeskadv.index.type') }}</th>
                    <th>{{ __('advertiser.servicedeskadv.index.status') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
