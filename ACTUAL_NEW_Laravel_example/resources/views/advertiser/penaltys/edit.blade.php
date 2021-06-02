@extends('layouts.app')

@section('title', __('advertiser.penaltys.edit.app.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.penaltys.edit.app.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.penaltys.index") }}" data-bs-toggle="modal"
               class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.penaltys.edit.to-list') }}
            </a>
        </x-slot>
        <div>
            <form id="createOrderForm" method="post" action="{{ route("advertiser.penaltys.update", $penalty) }}">
                @csrf
                @method('PATCH')
                <div>
                    <input type="hidden" name="order_id" value="{{ $penalty->order_id }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.type') }}</label>
                    <select name="type" class="form-select" required>
                        @foreach(\App\Lists\PenaltysTypesList::getList() as $k=>$v)
                            @if($k == $penalty->type)
                                <option value="{{ $k }}" selected>{{ $v }}</option>
                            @else
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.offer') }}</label>
                    <select name="offer_id" class="form-select" required>
                        @foreach(\App\Models\Offer::getOwnOffers(auth()->user()) as $offer)
                            @if($offer == $penalty->offer)
                                <option value="{{ $offer->id }}" selected>{{ $offer->offer_name }}</option>
                            @else
                                <option value="{{ $offer->id }}">{{ $offer->offer_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.partner') }}</label>
                    <select name="partner_id" class="form-select" required>
                        @foreach(\App\User::getPartners(auth()->user()) as $partner)
                            <option value="{{ $partner->id  }}">{{ $partner->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.order-sum') }}</label>
                    <input type="number" required step="0.01" name="gross_amount" class="form-control" value="{{ $penalty->gross_amount }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.order-date') }}</label>
                    <input name="datetime" required type="datetime-local" class="form-control" value="{{ $penalty->datetime->format('Y-m-d\TH:i:s') }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.comment') }}</label>
                    <input type="text" name="comment" class="form-control" value="{{ $penalty->comment }}">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.penaltys.edit.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>
@endsection
