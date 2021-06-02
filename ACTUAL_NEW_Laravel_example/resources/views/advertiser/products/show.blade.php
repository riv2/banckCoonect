@extends('layouts.app')

@section('title', __('Product'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('Product info') }}</x-slot>
        <x-slot name="rightblock">
{{--            <a href="{{ route(auth()->user()->role . ".products.edit" , $product->product_id) }}" data-bs-toggle="modal"--}}
{{--               class="btn btn-outline-primary btn-sm">--}}
{{--                {{ __('Edit') }}--}}
{{--            </a>--}}
        </x-slot>
        <div class="list-group list-group-flush">
            Тут будет описание продукта
        </div>
    </x-box>
@endsection
