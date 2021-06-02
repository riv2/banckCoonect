@extends('layouts.app')

@section('title', __('partners.servicedesk.index.app.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('partners.servicedesk.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("partner.servicedesk.create") }}" class="btn btn-outline-primary btn-sm">
                {{ __('partners.servicedesk.index.add-ticket') }}</a>
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
                    <th>{{ __('partners.servicedesk.index.id') }}</th>
                    <th>{{ __('partners.servicedesk.index.theme') }}</th>
                    <th>{{ __('partners.servicedesk.index.created_at') }}</th>
                    <th>{{ __('partners.servicedesk.index.answer-deadline') }}</th>
                    <th>{{ __('partners.servicedesk.index.type') }}</th>
                    <th>{{ __('partners.servicedesk.index.status') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection
