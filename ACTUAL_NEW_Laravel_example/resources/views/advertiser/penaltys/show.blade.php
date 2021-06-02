@extends('layouts.app')

@section('title', __('advertiser.penaltys.show.app.title'))

@section('content')
    Show document
    <x-box>
        <x-slot name="title">{{ __('advertiser.penaltys.show.app.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.penaltys.index") }}" data-bs-toggle="modal"
               class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.penaltys.edit.to-list') }}
            </a>
        </x-slot>
        <div>
            <form id="createOrderForm">
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.type') }}</label>
                    <div class="form-control">
                        {{ \App\Lists\PenaltysTypesList::getList()[$penalty->type] }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.offer') }}</label>
                    <div class="form-control">
                        {{ $penalty->offer->offer_name }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.partner') }}</label>
                    <div class="form-control">
                        {{ $penalty->partner->email }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.order-sum') }}</label>
                    <div class="form-control">
                        {{ $penalty->gross_amount }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.order-date') }}</label>
                    <div class="form-control">
                        {{ $penalty->datetime->format('Y-m-d \TH:i:s') }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.penaltys.edit.comment') }}</label>
                    <div class="form-control">
                        {{ $penalty->comment }}
                    </div>
                </div>
                <br>
                <div class="form-group">
                    <a href="{{ route('advertiser.penaltys.edit', $penalty->order_id) }}" data-bs-toggle="modal"
                       class="btn btn-outline-primary btn-sm">
                        {{ __('advertiser.penaltys.edit') }}
                    </a>
                </div>
            </form>
        </div>
    </x-box>
@endsection
