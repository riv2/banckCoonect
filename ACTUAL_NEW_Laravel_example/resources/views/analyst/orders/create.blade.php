@extends('layouts.app')

@section('title', __('advertiser.orders.create.app.title'))

@section('content')


    <x-box>
        <x-slot name="title">{{ __('advertiser.orders.create.app.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.index") }}" data-bs-toggle="modal" class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.orders.create.to-orders') }}
            </a>
        </x-slot>
        <div>
            <form id="createOrderForm" method="post" action="{{ route("advertiser.orders.store") }}">
                @csrf
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.order-id') }}</label>
                    <input type="text" name="order_id" required class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.offer') }}</label>
                    <select name="offer_id" class="form-select" required>
                        <option value="">{{ __('advertiser.orders.create.select') }}...</option>
                        @foreach(\App\Models\Offer::getOwnOffers(auth()->user()) as $offer)
                            <option value="{{ $offer->id }}">{{ $offer->offer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.partner') }}</label>
                    <select name="partner_id" class="form-select" required>
                        @foreach(\App\User::getPartners(auth()->user()) as $partner)
                            <option value="{{ $partner->id  }}">{{ $partner->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.link-id') }}</label>
                    <input type="number" required name="link_id" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.order-sum') }}</label>
                    <input type="number" required step="0.01" name="gross_amount" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.create.order-date') }}</label>
                    <input name="datetime" required type="datetime-local" class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.orders.create.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>


@endsection
