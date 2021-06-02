@extends('layouts.app')

@section('title', __('advertiser.orders.import.app-title'))

@section('content')
    <x-box id="app">
        <x-slot name="title">{{ __('advertiser.orders.import.title') }}</x-slot>
        <x-slot name="rightblock"></x-slot>
        <form action="{{ route('advertiser.orders.import') }}" method="post">
            @method('put')
            @csrf
            <import-xlsx></import-xlsx>
        </form>
    </x-box>
@endsection
