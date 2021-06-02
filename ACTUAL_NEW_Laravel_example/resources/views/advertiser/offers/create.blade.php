@extends('layouts.app')

@section('title', __('advertiser.offers.create.title'))

@section('content')
    <script>
        function change_fee() {
            var fee_type=document.getElementById("fee_type").value;
            if (fee_type==='fix') {
                document.getElementById("fee_label").innerText = "{{ __('advertiser.offers.create.fee_fix') }}
                    ({{ auth()->user()->pp->currency }})";
            } else if (fee_type==='share') {
                document.getElementById("fee_label").innerText = "{{ __('advertiser.offers.create.fee_share') }} (%)";
            }
        }
    </script>

    <x-box>
        <x-slot name="title">{{ __('advertiser.offers.create.new_offer') }}</x-slot>
        <form method="post" action="{{ route('advertiser.offers.store') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <x-input type="text" name="offer_name"
                     placeholder="{{ __('advertiser.offers.create.offer_name.placeholder') }}" required autofocus>
                {{ __('advertiser.offers.create.offer_name') }}
                <x-slot name="help">{{ __('advertiser.offers.create.offer_name.desc') }}</x-slot>
            </x-input>

            <x-input type="textarea" name="description" style="min-height:150px" placeholder="{{ __('advertiser.offers.create.desc.placeholder') }}" required>
                {{ __('advertiser.offers.create.desc') }}
                <x-slot name="help">{{ __('advertiser.offers.create.desc.desc') }}</x-slot>
            </x-input>

            <x-input type="url" name="url" :value="old('url')" placeholder="https://domain.com" required autofocus>
                {{ __('advertiser.offers.create.link') }}
                <x-slot name="help">{{ __('advertiser.offers.create.link.desc') }}</x-slot>
            </x-input>

            <x-input type="select" name="model" data-nullvalue :options="App\Lists\OrderStateList::getList('V')" required>
                {{ __('advertiser.offers.create.model') }}
                <x-slot name="help">{{ __('advertiser.offers.create.model') }}</x-slot>
            </x-input>

            <x-input type="select" id="fee_type" name="fee_type" onchange="change_fee()" required data-nullvalue :options="App\Lists\OffersFeeTypeList::getFeeTypeList()">
                {{ __('advertiser.offers.create.fee_type') }}
                <x-slot name="help">{{ __('advertiser.offers.create.fee_type.desc') }}</x-slot>
            </x-input>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="form-label-group">
                        <label class="form-label" id="fee_label">
                            {{ __('advertiser.offers.create.fee_label') }}
                        </label>
                        <input class="form-control" type="number" name="fee" step="0.01" required="required">
                    </div>
                </div>
                <div class="col-md-6 small">
                    <section class="pt-md-2">
                        {{ __('advertiser.offers.create.fee_label.desc') }}
                    </section>
                </div>
            </div>

            <x-input type="file" name="image">
                {{ __('advertiser.offers.create.logo') }}
                <x-slot name="help">{{ __('advertiser.offers.create.logo.desc') }}</x-slot>
            </x-input>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">{{ __('advertiser.offers.create.save') }}</button>
            </div>
        </form>
    </x-box>

@endsection
