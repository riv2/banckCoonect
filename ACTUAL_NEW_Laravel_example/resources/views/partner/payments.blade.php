@extends('layouts.app')

@section('title', __('partners.payments.app.title'))

@section('content')
    <div class="row mb-5">
        <div class="col-md-4 mb-3">
            <div class="d-block card border-start-primary shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div
                        class="d-block font-weight-bold text-primary text-uppercase text-xs mb-1">
                        {{ __('partners.payments.total') }}:
                    </div>
                    <div class="mb-0 font-weight-bold">
                        @money($totals["all"])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="d-block card border-start-success shadow h-100 py-2 text-decoration-none">
                <div class="card-body">
                    <div class="d-block font-weight-bold text-success text-uppercase text-xs mb-1">
                        {{ __('partners.payments.fee') }}:</div>
                    <div class="mb-0 font-weight-bold">
                        <div class="row row-cols-3 no-gutters text-body">
                            <div class="col">@money($totals[1])</div>
                            <div class="col">@money($totals[0])</div>
                            <div class="col">@money($totals[2])</div>
                            <div class="col text-xs text-secondary small text-nowrap">{{ __('partners.payments.paid-up') }}</div>
                            <div class="col text-xs text-secondary small text-nowrap">{{ __('partners.payments.awaiting-payment') }}</div>
                            <div class="col text-xs text-secondary small text-nowrap">{{ __('partners.payments.awaiting-confirmation') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-5">
        <div class="col">
            {{ __('partners.payments.dear-partner') }}.<br>
            {{ __('partners.payments.change-pay-method') }} <a href="{{ route("partner.profile.index") }}">{{ __('partners.payments.profile.r') }}</a>
        </div>
    </div>

    @if(!count($payments))
        <div class="alert alert-warning">
            <strong>{{ __('partners.payments.no-payments') }}!</strong>
            <p class="mb-0">{{ __('partners.payments.to-get-payed') }} &laquo;{{ __('partners.payments.profile') }}&raquo;</p>
        </div>
    @else
        <h3>{{ __('partners.payments.history') }}:</h3>
        <table class="table table-sm">
            <thead>
            <tr>
                <td>{{ __('partners.payments.date') }}</td>
                <td>{{ __('partners.payments.pay-method') }}</td>
                <td>{{ __('partners.payments.requisites') }}</td>
                <td>{{ __('partners.payments.for-the-period') }}</td>
                <td>{{ __('partners.payments.sum') }}</td>
                <td>{{ __('partners.payments.detailing') }}</td>
                <td>{{ __('partners.payments.status') }}</td>
                <td></td>
            </tr>
            </thead>
            <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->datetime }}</td>
                    <td>@if($payment->payMethod){{ $payment->payMethod->caption }} @else Не выбран @endif</td>
                    <td>@if($payment->payAccount){{ $payment->payAccount->company_name }}@else  @endif</td>
                    <td>{{ $payment->datetime->format('F Y') }}</td>
                    <td>{{ $payment->revenue }} {{ auth()->user()->pp->currency }}</td>
                    <td><a href="{{ route('partner.orders.index', ['reestr_id' =>$payment->reestr_id] ) }}">{{ __('partners.payments.detailing') }}</a></td>
                    <td>{{ $statuses[$payment->status] }}</td>
                    <td><a href="#">{{ __('partners.payments.challenge') }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif

@endsection
