@extends('layouts.app')

@section('title', __('advertiser.welcome.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.welcome.title') }}</x-slot>

        <form action="{{ route('advertiser.welcome.store') }}" method="post">
            @csrf
            @method('PUT')

            <x-input type="url" name="company_url" :value="old('company_url', $pp->company_url)"
                     placeholder="https://domain.com" required autofocus>
                {{ __('advertiser.welcome.company_url') }}
                <x-slot name="help">{{ __('advertiser.welcome.company_url-help') }}</x-slot>
            </x-input>

            <x-input type="select" name="pp_target" :options="App\Lists\PpTargetList::getList()" required>
                {{ __('advertiser.welcome.pp_target') }}
                <x-slot name="help">
                    {{ __('advertiser.welcome.select') }}
                    <strong>{{ __('advertiser.welcome.leads') }}</strong> {{ __('advertiser.welcome.leads-condition') }}
                    <br/>
                    {{ __('advertiser.welcome.select') }}
                    <strong>{{ __('advertiser.welcome.orders') }}</strong> {{ __('advertiser.welcome.orders-condition') }}
                </x-slot>
            </x-input>

            <x-input type="select" name="currency" :options="App\Lists\PpCurrencyList::getList()" required>
                {{ __('advertiser.welcome.currency') }}
                <x-slot name="help">
                    {{ __('advertiser.welcome.currency-help') }}
                </x-slot>
            </x-input>

            <x-input type="textarea" name="comment" style="min-height:150px"
                     placeholder="{{ __('advertiser.welcome.comment-placeholder') }}">
                {{ __('advertiser.welcome.comment') }}
                <x-slot name="help">{{ __('advertiser.welcome.comment-help') }}</x-slot>
            </x-input>

            <div class="mb-5">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.welcome.submit') }}</button>
            </div>

        </form>
    </x-box>
@endsection
