@extends('layouts.app')

@section('title', __('advertiser.integration.cms.index.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.integration.cms.index.title') }}</x-slot>
        <x-slot name="rightblock">
            <a href="{{ route("advertiser.servicedeskadv.create") }}?type=technical&subject=%D0%98%D0%BD%D1%82%D0%B5%D0%B3%D1%80%D0%B0%D1%86%D0%B8%D1%8F%20%D0%BF%D0%BE%20API"
               class="btn btn-primary btn-sm">{{ __('advertiser.integration.api.right-button') }}</a>
        </x-slot>
        @foreach($cmsSystems as $cms)
            <a href="{{ route('advertiser.integration.cms.' . $cms['name']) }}" class="text-decoration-none">
                <div class="card " style="width: 18rem;">
                    <img src="{{ $cms['img'] }}" class="card-img-top px-5 pt-5" alt="...">
                    <div class="card-body ">
                        <p class="card-text text-center text-dark text-capitalize">{{ $cms['name'] }}</p>
                    </div>

                </div>
            </a>
        @endforeach
    </x-box>
@endsection
