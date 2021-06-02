@extends('layouts.app')

@section('title', __('advertiser.tariff.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.tariff.title') }}</x-slot>

        {{ __('advertiser.tariff.your-tariff') }}: <span
            class="text-uppercase font-weight-bold">{{ auth()->user()->pp->tariff }}</span>
        <br/><a class="no_a" target="_blank"
                href="https://gocpa.ru/gocpa-cloud#prices">{{ __('advertiser.tariff.tariff-desc') }}</a>
        <br/>
        <br/>
        @if (auth()->user()->pp->tariff == 'free')
            {{ __('advertiser.tariff.max-partners') }}: <strong>5</strong><br/>
            {{ __('advertiser.tariff.registered-partners') }}: <strong>{{$partners_cnt}}</strong>
        @elseif (auth()->user()->pp->tariff == 'start')
            {{ __('advertiser.tariff.max-partners') }}: <strong>10</strong><br/>
            {{ __('advertiser.tariff.registered-partners') }}: <strong>{{$partners_cnt}}</strong>
        @elseif (auth()->user()->pp->tariff == 'professional')
            {{ __('advertiser.tariff.max-partners') }}: <strong>100</strong><br/>
            {{ __('advertiser.tariff.registered-partners') }}: <strong>{{$partners_cnt}}</strong>
        @endif
        <br/><br/>
        <a href="{{ route("advertiser.servicedeskadv.create") }}?type=commercial&subject=%D0%A1%D0%BC%D0%B5%D0%BD%D0%B0%20%D1%82%D0%B0%D1%80%D0%B8%D1%84%D0%B0"
           class="btn btn-primary btn-md">{{ __('advertiser.tariff.change-tariff') }}</a>
    </x-box>
@endsection
