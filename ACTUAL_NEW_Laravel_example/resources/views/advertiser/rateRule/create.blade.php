@extends('layouts.app')

@section('title', __('advertiser.rateRule.create.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.rateRule.create.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.offers.show", request('offer_id')) }}" class="btn btn-sm btn-outline-primary">{{ __('advertiser.rateRule.create.right-button') }}</a>
        </x-slot>

        <form method="post" enctype="multipart/form-data" action="{{ route('advertiser.rateRule.store') }}">
            @csrf
            <input type="hidden" name="offer_id" value="{{ request('offer_id') }}">

            <x-input type="number" name="fee" data-nullvalue step=".01" min="0" required autofocus>
                {{ __('advertiser.rateRule.create.fee') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.fee-help') }}</x-slot>
            </x-input>

            <x-input type="date" name="date_start" required>
                {{ __('advertiser.rateRule.create.date-start') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.date-start-help') }}</x-slot>
            </x-input>

            <x-input type="date" name="date_end" data-nullvalue>
                {{ __('advertiser.rateRule.create.date-end') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.date-end-help') }}</x-slot>
            </x-input>

            <x-input type="number" name="business_unit_id" data-nullvalue>
                {{ __('advertiser.rateRule.create.business-unit-id') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.business-unit-id-help') }}</x-slot>
            </x-input>

            <x-input type="select" name="link_id" :options="$links" data-nullvalue>
                {{ __('advertiser.offers.show.link_name') }}
                <x-slot name="help">{{ __('advertiser.offers.show.link_name') }}</x-slot>
            </x-input>

            <x-input type="select" name="partner_id" :options="$users"
                    data-nullvalue>
                {{ __('advertiser.offers.show.partner_name') }}
                <x-slot name="help">{{ __('advertiser.offers.show.partner_name') }}</x-slot>
            </x-input>

            <x-input type="select" name="progressive_param" :options="$availableProgressiveParams"
                     data-nullvalue>
                {{ __('advertiser.rateRule.create.progressive-param') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.progressive-param-help') }}</x-slot>
            </x-input>

            <x-input type="number" name="progressive_value" data-nullvalue step="1"
                     min="0">
                {{ __('advertiser.rateRule.create.progressive-value') }}
                <x-slot name="help">{{ __('advertiser.rateRule.create.progressive-value-help') }}</x-slot>
            </x-input>
            <button type="submit" class="btn btn-primary">{{ __('advertiser.rateRule.create.submit') }}</button>
        </form>
    </x-box>

@endsection

