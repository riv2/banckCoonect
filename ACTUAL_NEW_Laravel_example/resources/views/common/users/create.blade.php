@extends('layouts.app')

@section('title', 'Создание пользователя')

@section('content')
<x-box>
    <x-slot name="title">Создание пользователя</x-slot>
    <x-slot name="rightblock"></x-slot>
    <form action="{{ route(auth()->user()->role . '.users.store') }}" method="post">
        @csrf

        <div class="form-group mb-3 row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __("Name") }}</label>

            <div class="col-md-6">
                <input id="name" type="name" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name">

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group mb-3 row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __("E-mail") }}</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary" type="submit">Отправить</button>
        </div>
    </form>
</x-box>
@endsection
