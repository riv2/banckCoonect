@extends('layouts.app')

@section('title', __('advertiser.banned_frauds.show.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_frauds.show.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route('advertiser.banned-frauds.index') }}" class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.banned_frauds.show.to-list') }}
            </a>
        </x-slot>
        <div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_frauds.show.order-id') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        {{ $bannedFraud->order_id }}
                    </p>
                </div>
            </div>
            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_frauds.show.offer-id') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        #{{ $bannedFraud->offer_id }}
                    </p>
                </div>
            </div>

            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_frauds.show.partner') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        #{{ $order->partner->id . ' ' .  $order->partner->name }}
                    </p>
                </div>
            </div>

            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_frauds.show.comment') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        {{ $bannedFraud->comment }}
                    </p>
                </div>
            </div>

            <div class="row border-bottom">
                <div class="col-md-3 small align-self-center text-secondary">
                    <p class="my-2">{{ __('advertiser.banned_frauds.show.evidence') }}</p>
                </div>
                <div class="col-md-9">
                    <p class="my-2">
                        {{ $bannedFraud->evidence }}
                    </p>
                </div>
            </div>

            <form action="{{ route("advertiser.banned-frauds.destroy", $bannedFraud) }}" method="post">
                @csrf
                @method('DELETE')
                <div class="mt-4">
                    <button class="btn btn-danger"
                            type="submit">{{ __('advertiser.banned_frauds.show.destroy') }}</button>
                </div>
            </form>
        </div>
    </x-box>
@endsection
