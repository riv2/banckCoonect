@extends('layouts.app')

@section('title', __('advertiser.partners.create.app.title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.partners.create.title') }}</x-slot>

        <form action="{{ route('advertiser.partners.store') }}" method="post">
            @csrf
            <div class="mb-3">
                <label class="form-label">{{ __('advertiser.partners.create.name') }}</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required autocomplete="name" autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('advertiser.partners.create.email') }}</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" required autocomplete="email">
            </div>
            <div class="mb-3">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.partners.create.add') }}</button>
            </div>
        </form>
    </x-box>
@endsection
