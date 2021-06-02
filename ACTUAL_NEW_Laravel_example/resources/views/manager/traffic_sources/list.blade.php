@extends('layouts.app')

@section('title', __('manager.traffic_sources.list.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('manager.traffic_sources.list.app-title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("manager.traffic.sources.new") }}"
               class="btn btn-outline-primary btn-sm">{{ __('manager.traffic_sources.list.add') }}</a>
        </x-slot>

        @php
            $format = [
                'id' => 'number',
                'view_link' => 'html',
            ];
        @endphp
        <x-table :data="$sources" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>#</th>
                    <th>{{ __('manager.traffic_sources.list.title') }}</th>
                </tr>
            </x-slot>
        </x-table>
    </x-box>
@endsection

