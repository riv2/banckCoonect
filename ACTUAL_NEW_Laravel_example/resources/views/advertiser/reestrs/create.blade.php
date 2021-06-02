@extends('layouts.app')

@section('title', __('advertiser.registries.create.app-title'))

@section('content')

<x-box>
    <a href="{{ route("advertiser.reestrs.index") }}">{{ __('advertiser.registries.create.to-reg-list') }}</a>
</x-box>
<x-box>
    <x-slot name="title">{{ __('advertiser.registries.create.statuses') }}</x-slot>
    @if($updates_stopped)
        <form method="post" action="{{ route("advertiser.reestrs.start-update") }}">
            {{ __('advertiser.registries.statuses.update-stopped') }}
            @csrf
            <button type="submit" class="btn btn-primary">{{ __('advertiser.registries.create.start-statuses-update') }}</button>
        </form>
    @else
    <form method="post" action="{{ route("advertiser.reestrs.stop-update") }}">
        {{ __('advertiser.registries.statuses.update-started') }}
        @csrf
        <button type="submit" class="btn btn-primary">{{ __('advertiser.registries.create.stop-statuses-update') }}</button>
    </form>
    @endif
</x-box>
<x-box>
    <x-slot name="title">{{ __('advertiser.registries.create.new-reg') }}</x-slot>
    <form method="post" action="{{ route("advertiser.reestrs.store") }}">
        @csrf
        <button type="submit" class="btn btn-primary">{{ __('advertiser.registries.create.create-new-reg') }}</button>
    </form>
</x-box>

@endsection
