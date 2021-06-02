@extends('layouts.app')

@section('title', __('advertiser.banned_frauds.create.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_frauds.create.title') }}</x-slot>
        <x-slot name="rightblock"></x-slot>
        <form action="{{ route("advertiser.banned-frauds.store") }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_frauds.create.order-id') }}</label>
                        <input type="text" name="order_id"
                               class="form-control @error('order_id') is-invalid @enderror"
                               value="{{ old('order_id') }}" min="5" required>
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_frauds.create.order-id-desc') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_frauds.create.offer') }}</label>
                        <select name="offer_id" class="form-select" required>
                            <option value="">{{ __('advertiser.orders.create.select') }}...</option>
                            @foreach(\App\Models\Offer::getOwnOffers(auth()->user()) as $offer)
                                <option value="{{ $offer->id }}">{{ $offer->offer_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_frauds.create.offer-id-desc') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_frauds.create.comment') }}</label>
                        <textarea name="comment" rows="3"
                                  class="form-control @error('comment') is-invalid @enderror required"
                                  required>{{ old('comment') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 small align-self-top">
                    <p>{{ __('advertiser.banned_frauds.create.comment-desc') }}</p>
                    <p>{{ __('advertiser.banned_frauds.create.comment-desc1') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_frauds.create.evidence') }}</label>
                        <textarea name="evidence" rows="3"
                                  class="form-control @error('evidence') is-invalid @enderror required"
                                  required>{{ old('evidence') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 small align-self-top">
                    <p>{{ __('advertiser.banned_frauds.create.evidence-desc') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.banned_frauds.create.send') }}</button>
            </div>
        </form>
    </x-box>
@endsection
