@extends('layouts.app')

@section('title', __('advertiser.products.create.app.title'))

@section('content')


    <x-box>
        <x-slot name="title">{{ __('advertiser.products.create.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.index") }}" data-bs-toggle="modal" class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.products.create.to-orders') }}
            </a>
        </x-slot>
        <div>
            <form id="createProductForm" method="post" action="{{ route("advertiser.orders.products.store", $order) }}">
                @csrf
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.create.order-id') }}</label>
                    <input type="text" name="product_id"  required value="{{ old('product_id') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.create.name') }}</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.create.price') }}</label>
                    <input type="number" required step="0.01" name="price" value="{{ old('price') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.create.quantity') }}</label>
                    <input type="number" required step="1" name="quantity" value="{{ old('quantity') }}"
                           class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.products.create.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>


@endsection
