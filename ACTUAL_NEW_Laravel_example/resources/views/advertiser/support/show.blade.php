@extends('layouts.app')

@section('title', __('advertiser.support.show.app-title'))

@section('content')
    <x-box>
        <div>{{ $support->case_description }}</div>
        <br>
        @if(empty($support->answer))
            {{ __('advertiser.support.show.no-answer') }}
        @else
        <div>{{ $support->answer }}</div>
        @endif
    </x-box>
@endsection

