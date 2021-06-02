@extends('layouts.app')

@section('title', __('partners.offers.new.title'))

@section('content')
    <form method="post" action="{{ route("partner.links.create") }}">
        @csrf
        <input type="hidden" name="id" value="{{ $offer->id }}">
        <div class="form-group">
            <label for="">{{ __('partners.offers.new.link_name') }}</label>
            <input type="text" name="name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary" onclick="saveTheLink()">{{ __('partners.offers.new.save') }}</button>
    </form>


@endsection

