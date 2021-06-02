@extends('layouts.app')
@section('title', __('advertiser.products.edit.app.title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.products.edit.title') }}:
            <span>Id = {{ $product->product_id }}</span>
        </x-slot>
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
                    <input type="hidden" name="product_id" value="{{ $product->product_id }}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.edit.name') }}</label>
                    <input type="text" name="product_name" value="{{$product->product_name}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.edit.product-status') }}</label>
                    <select name="status" class="form-select" required>
                        @foreach(\App\Lists\OrdersProductStateList::getList() as $key=>$value)
                            @if($key == $product->status)
                                <option value="{{ $key }}" selected>{{ $value }}</option>
                            @else
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.edit.price') }}</label>
                    <input type="number" required step="0.01" name="price" value="{{$product->price}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.edit.quantity') }}</label>
                    <input type="number" required step="1" name="quantity" value="{{$product->quantity}}"
                           class="form-control">
                </div>
                <div class="form-group">
                    <label for="">{{ __('advertiser.products.edit.product-sum') }}</label>
                    <input type="number" required step="0.01" name="amount" value="{{$product->amount}}"
                           class="form-control">
                </div>
                <br>
                <div class="form-group">
                    <button class="btn btn-primary" type="submit">{{ __('advertiser.products.edit.save') }}</button>
                </div>
            </form>
        </div>
    </x-box>

@endsection
