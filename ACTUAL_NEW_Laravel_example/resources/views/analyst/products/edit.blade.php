@extends('layouts.app')

@section('title', __('Edit Product'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('Edit product') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.index") }}" data-bs-toggle="modal"
               class="btn btn-outline-primary btn-sm">
                {{ __('advertiser.orders.edit.to-orders') }}
            </a>
        </x-slot>
        <div>
            <form id="createProductForm" method="post"
                  action="{{ route("advertiser.orders.products.update", [$product->order_id, $product->product_id]) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <input type="hidden" name="order_id" value="{{ $product->order_id }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Product ID') }}</label>
                    <input type="text" name="product_id" value="{{ $product->product_id }}" required
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Product Name') }}</label>
                    <input type="text" name="product_name" value="{{$product->product_name}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Price') }}</label>
                    <input type="number" required step="0.01" name="price" value="{{$product->price}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Quantity') }}</label>
                    <input type="number" required step="1" name="quantity" value="{{$product->quantity}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Sum') }}</label>
                    <input type="number" required step="0.01" name="amount" value="{{$product->amount}}"
                           class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </x-box>

@endsection
