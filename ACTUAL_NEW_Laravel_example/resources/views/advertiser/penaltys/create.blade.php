@extends('layouts.app')

@section('title', __('advertiser.penaltys.create.app.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.penaltys.create.app.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.penaltys.index") }}" data-bs-toggle="modal"
               class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.penaltys.create.to-list') }}
            </a>
        </x-slot>
        <div>
            <form id="createOrderForm" method="post" action="{{ route("advertiser.penaltys.store") }}">
                @csrf
                <div class="form-group">
                    <input type="hidden" name="order_id" value="{{ \Illuminate\Support\Str::uuid() }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.type') }}</label>
                    <select name="type" class="form-select" required>
                        <option value="">{{ __('advertiser.penaltys.create.select') }}...</option>
                        @foreach(\App\Lists\PenaltysTypesList::getList() as $k=>$v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.offer') }}</label>
                    <select name="offer_id" class="form-select" required>
                        <option value="">{{ __('advertiser.penaltys.create.select') }}...</option>
                        @foreach(\App\Models\Offer::getOwnOffers(auth()->user()) as $offer)
                            <option value="{{ $offer->id }}">{{ $offer->offer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.partner') }}</label>
                    <select name="partner_id" class="form-select" required>
                        <option value="">{{ __('advertiser.penaltys.create.select') }}...</option>
                        @foreach(\App\User::getPartners(auth()->user()) as $partner)
                            <option value="{{ $partner->id  }}">{{ $partner->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.order-sum') }}</label>
                    <input type="number" required step="0.01" name="gross_amount" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.order-date') }}</label>
                    <input name="datetime" required type="datetime-local" class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.create.comment') }}</label>
                    <input type="text" name="comment" class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.penaltys.create.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>
@endsection
