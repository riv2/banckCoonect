@extends('layouts.app')

@section('title', __('advertiser.orders.edit.app.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.orders.edit.app.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.index") }}" data-bs-toggle="modal" class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.orders.edit.to-orders') }}
            </a>
        </x-slot>
        <div>
            <form id="editOrderForm" method="POST" action="{{ route("advertiser.orders.update", $order) }}">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.order-status') }}</label>
                    <select name="status" class="form-select" required>
                        @foreach(\App\Lists\OrderStateList::getList() as $key=>$value)
                            @if($key == $order->status)
                                <option value="{{ $key }}" selected>{{ $value }}</option>
                            @else
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.order-date-time') }}</label>
                    <input name="datetime" required type="datetime-local" class="form-control"
                           value="{{ $order->datetime->format('Y-m-d\TH:i:s') }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.order-partner') }}</label>
                    <select name="partner_id" class="form-select" required>
                        @foreach(\App\User::getPartners(auth()->user()) as $partner)
                            @if($partner->id == $order->partner_id)
                                <option value="{{ $partner->id  }}" selected>{{ $partner->email }}</option>
                            @else
                                <option value="{{ $partner->id  }}">{{ $partner->email }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.link-id') }}</label>
                    <input type="number" required name="link_id" class="form-control" value="{{ $order->link_id }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.click-id') }}</label>
                    <input type="text" name="click_id" class="form-control" value="{{ $order->click_id }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.web-id') }}</label>
                    <input type="text" name="web_id" class="form-control" value="{{ $order->web_id }}">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.orders.edit.client-id') }}</label>
                    <input type="text" name="client_id" class="form-control" value="{{ $order->client_id }}">
                </div>
                <div class="form-group pt-1">
                    <label for="">{{ __('advertiser.orders.edit.wholesale') }}</label>
                    <select name="wholesale" class="form-select" required>
                            @if($order->wholesale == 1)
                            <option value="1" selected>{{ __('advertiser.orders.edit.yes') }}</option>
                            <option value="0">{{ __('advertiser.orders.edit.no') }}</option>
                            @else
                            <option value="1">{{ __('advertiser.orders.edit.yes') }}</option>
                            <option value="0" selected>{{ __('advertiser.orders.edit.no') }}</option>
                            @endif
                    </select>
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.orders.edit.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>
@endsection
