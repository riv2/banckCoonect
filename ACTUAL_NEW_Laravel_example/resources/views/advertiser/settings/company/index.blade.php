@extends('layouts.app')

@section('title', __('advertiser.settings.company.index.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.company.index.title') }}</x-slot>

        <form action="{{ route('advertiser.settings.company.update') }}" method="post">
            @csrf
            @method('PUT')

            <x-input type="url" name="company_url" :value="$pp->company_url" placeholder="https://domain.com" required autofocus>
                {{ __('advertiser.settings.company.index.web-adress') }}
                <x-slot name="help">{{ __('advertiser.settings.company.index.insert-web-adress') }}</x-slot>
            </x-input>

            <x-input type="select" name="pp_target" :value="$pp->pp_target" :options="App\Lists\PpTargetList::getList()" required>
                {{ __('advertiser.settings.company.index.what-paying-for') }}
                <x-slot name="help">
                    {{ __('advertiser.settings.company.index.choose') }}
                    <strong>{{ __('advertiser.settings.company.index.leads') }}</strong>
                    {{ __('advertiser.settings.company.index.leads-condition') }}<br/>
                    {{ __('advertiser.settings.company.index.choose') }}
                    <strong>{{ __('advertiser.settings.company.index.orders') }}</strong>
                    {{ __('advertiser.settings.company.index.orders-condition') }}<br/>
                </x-slot>
            </x-input>

            <x-input type="select" name="currency" :value="$pp->currency" :options="App\Lists\PpCurrencyList::getList()" required>
                {{ __('advertiser.settings.company.index.select-currency') }}
                <x-slot name="help">
                    {{ __('advertiser.settings.company.index.select-currency-desc') }}
                </x-slot>
            </x-input>



            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.company.index.tech-domain') }}</label>
                        <input type="text" class="form-control" value="{{ $pp->tech_domain }}" readonly disabled>
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.company.index.prod-domain') }}</label>
                        <input type="text" name="prod_domain"
                               class="form-control @error('prod_domain') is-invalid @enderror"
                               value="{{ $pp->prod_domain }}">
                    </div>
                </div>
                <div class="col-md-6 small">
                    <p>{{ __('advertiser.settings.company.index.you-can-choose-your-domain') }}</p>
                    <p>{{ __('advertiser.settings.company.index.connection-condition') }}</p>
                    <p>{{ __('advertiser.settings.company.index.set-dns') }}:</p>
                    <table class="table">
                        <tr>
                            <th>{{ __('advertiser.settings.company.index.record-type') }}</th>
                            <th>{{ __('advertiser.settings.company.index.sub-domain-name') }}</th>
                            <th>{{ __('advertiser.settings.company.index.value') }}</th>
                        </tr>
                        <tr>
                            <th>{{ __('advertiser.settings.company.index.cname') }}</th>
                            <td><code>{{ __('advertiser.settings.company.index.partners') }}</code></td>
                            <td><code>{{ $pp->tech_domain }}.</code></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mb-5">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.settings.company.index.save') }}</button>
            </div>

        </form>
    </x-box>
@endsection
