@extends('layouts.app')

@section('title', __('manager.report.app-title'))

@section('content')

<div class="row">
    <x-box class="col-lg-3">
        <x-slot name="title">{{ __('manager.report.income') }}</x-slot>
        <x-slot name="rightblock">
            <span class="badge bg-primary small font-weight-light">{{ __('manager.report.for-current-month') }}</span>
        </x-slot>
        <div>
            <div class="fs-5 m-0">-</div>
            <div class="stat-percent font-bold text-primary float-end">-%</div>
            <small>{{ __('manager.report.base-tariff') }}</small>
        </div>
    </x-box>
    <x-box class="col-lg-3">
        <x-slot name="title">{{ __('manager.report.tariffs') }}</x-slot>
        <x-slot name="rightblock">
            <span class="badge bg-danger small font-weight-light">{{ __('manager.report.for-all-time') }}</span>
        </x-slot>
        <div>
            <div class="row g-0">
                <div class="col text-center border-end">
                    <div class="fs-5 m-0">@number($tariff_count['free'] ?? 0)</div>
                    <div class="small">{{ __('manager.report.try') }}</div>
                </div>
                <div class="col text-center border-end">
                    <div class="fs-5 m-0">@number($tariff_count['start'] ?? 0)</div>
                    <div class="small">{{ __('manager.report.start') }}</div>
                </div>
                <div class="col text-center">
                    <div class="fs-5 m-0">@number($tariff_count['professional'] ?? 0)</div>
                    <div class="small">{{ __('manager.report.prof') }}</div>
                </div>
            </div>
        </div>
    </x-box>
    <x-box class="col-lg-3">
        <x-slot name="title">{{ __('manager.report.advertisers') }}</x-slot>
        <x-slot name="rightblock">
            <span class="badge bg-success small font-weight-light">{{ __('manager.report.for-all-time') }}</span>
        </x-slot>
        <div>
            <h1 class="m-0">@number($advertisers_count)</h1>
            <div class="stat-percent font-bold text-success float-end">-%</div>
            <small>{{ __('manager.report.new-for-today') }}</small>
        </div>
    </x-box>
    <x-box class="col-lg-3">
        <x-slot name="title">{{ __('manager.report.partners') }}</x-slot>
        <x-slot name="rightblock">
            <span class="badge bg-info small font-weight-light">{{ __('manager.report.for-all-time') }}</span>
        </x-slot>
        <div>
            <h1 class="m-0">@number($partners_count)</h1>
            <div class="stat-percent font-bold text-info float-end">-%</div>
            <small>{{ __('manager.report.new-for-today') }}</small>
        </div>
    </x-box>
</div>

<x-box>
    <x-slot name="title">{{ __('manager.report.pp-list') }}</x-slot>

    @php
        $format = [
            'id' => 'format.number',
            'tech_domain' => '',
            'prod_domain' => '',
            'short_name' => '',
            'pp_owner' => 'html',
            'onboarding_status_text' => '',
            'pp_target_text' => '',
            'tariff_text' => '',
            'created_at' => 'format.datetime',
        ];
    @endphp
    <x-table :data="$pps" :format="$format">
        <x-slot name="thead">
            <tr>
                <th>ID</th>
                <th>{{ __('manager.report.tech-domain') }}</th>
                <th>{{ __('manager.report.prod-domain') }}</th>
                <th>{{ __('manager.report.short-name') }}</th>
                <th>{{ __('manager.report.owner_email') }}</th>
                <th>{{ __('manager.report.onboarding-status') }}</th>
                <th>{{ __('manager.report.target') }}</th>
                <th>{{ __('manager.report.tariff') }}</th>
                <th>{{ __('manager.report.reg-date') }}</th>
            </tr>
        </x-slot>
    </x-table>
</x-box>
@endsection
