@extends('layouts.app')

@section('title', __('partners.links.app.title'))

@section('content')
<x-box>
    <x-slot name="title">{{ __('partners.links.title') }}</x-slot>

    @php
        $format = [
            'link_name' => 'string',
            'created_at' => 'format.date',
            'offer_name' => 'string',
            'link' => 'html',
            'copy_link' => 'html',
        ];
    @endphp
    <x-table :format="$format" :data="$links">
        <x-slot name="thead">
            <tr>
                <th>{{ __('partners.links.name') }}</th>
                <th>{{ __('partners.links.added') }}</th>
                <th>{{ __('partners.links.offer') }}</th>
                <th>{{ __('partners.links.link') }}</th>
                <th></th>
            </tr>
        </x-slot>
    </x-table>
</x-box>
@endsection
