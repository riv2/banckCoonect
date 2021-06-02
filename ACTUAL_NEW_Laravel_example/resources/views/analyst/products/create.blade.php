@extends('layouts.app')

@section('title', __('advertiser.orders.create.app.title'))

@section('content')


    <x-box>
        <x-slot name="title">{{ __('Add product') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.orders.index") }}" data-bs-toggle="modal" class="btn btn-outline-primary btn-sm">
                {{ __('Add product') }}
            </a>
        </x-slot>
        <div>
            <form id="createProductForm" method="post" action="{{ route("advertiser.orders.products.store", $order) }}">
                @csrf
                <div class="form-group">
                    <label for="">{{ __('Product ID') }}</label>
                    <input type="text" name="product_id"  required value="{{ old('product_id') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Product Name') }}</label>
                    <input type="text" name="product_name" value="{{ old('product_name') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Price') }}</label>
                    <input type="number" required step="0.01" name="price" value="{{ old('price') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Quantity') }}</label>
                    <input type="number" required step="1" name="quantity" value="{{ old('quantity') }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('Sum') }}</label>
                    <input type="number" required step="0.01" name="amount" value="0"
                           class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.orders.create.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>


@endsection
