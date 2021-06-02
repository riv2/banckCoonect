@extends('layouts.app')

@section('title', __('advertiser.rateRule.edit.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.rateRule.edit.app-title') }} {{ $rateRule->id }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('advertiser.offers.show', $rateRule->offer_id) }}" class="btn btn-sm btn-outline-primary">
                {{ __('advertiser.rateRule.edit.right-button') }}
            </a>
        </x-slot>

        <form method="post" action="{{ route("advertiser.rateRule.update", request("rateRule")) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-input type="number" name="fee" :value="$rateRule->fee ?? ''" data-nullvalue step=".01" min="0" required autofocus>
                {{ __('advertiser.rateRule.edit.fee') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.fee-help') }}</x-slot>
            </x-input>

            <x-input type="date" name="date_start" :value="$rateRule->date_start->format('Y-m-d')" required>
                {{ __('advertiser.rateRule.edit.date-start') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.date-start-help') }}</x-slot>
            </x-input>

            <x-input type="date" name="date_end"
                     :value="$rateRule->date_end ? $rateRule->date_end->format('Y-m-d') : ''" data-nullvalue>
                {{ __('advertiser.rateRule.edit.date-end') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.date-end-help') }}</x-slot>
            </x-input>

            <x-input type="number" name="business_unit_id" :value="$rateRule->business_unit_id" data-nullvalue>
                {{ __('advertiser.rateRule.edit.business-unit-id') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.business-unit-id-help') }}</x-slot>
            </x-input>

            <x-input type="select" name="link_id" :options="$links"
                     :value="$rateRule->link_id" data-nullvalue>
                {{ __('advertiser.offers.show.link_name') }}
                <x-slot name="help">{{ __('advertiser.offers.show.link_name') }}</x-slot>
            </x-input>

            <x-input type="select" name="partner_id" :options="$users"
                     :value="$rateRule->partner_id" data-nullvalue>
                {{ __('advertiser.offers.show.partner_name') }}
                <x-slot name="help">{{ __('advertiser.offers.show.partner_name') }}</x-slot>
            </x-input>

            <x-input type="select" name="progressive_param" :options="$rateRule->getAvailableProgressiveParam()"
                     :value="$rateRule->progressive_param" data-nullvalue>
                {{ __('advertiser.rateRule.edit.progressive-param') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.progressive-param-help') }}</x-slot>
            </x-input>

            <x-input type="number" name="progressive_value" :value="$rateRule->progressive_value ?? ''" data-nullvalue step="1"
                     min="0" >
                {{ __('advertiser.rateRule.edit.progressive-value') }}
                <x-slot name="help">{{ __('advertiser.rateRule.edit.progressive-value-help') }}</x-slot>
            </x-input>

            <button class="btn btn-primary" type="submit">{{ __('advertiser.rateRule.edit.submit') }}</button>
        </form>
    </x-box>

@endsection
