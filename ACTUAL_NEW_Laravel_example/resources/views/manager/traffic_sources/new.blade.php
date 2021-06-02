@extends('layouts.app')

@section('title', __('manager.traffic_sources.app-title'))

@section('content')
    @if(isset($source))
        <strong>{{ __('manager.traffic_sources.edit-source') }}</strong>
    @else
        <strong>{{ __('manager.traffic_sources.add-source') }}</strong>
    @endif
    <form method="post" action="{{ route("manager.traffic.sources.save") }}">
        @csrf
        <input type="hidden" name="id" @if(isset($source)) value="{{ $source->id }}" @else value="" @endif>
        <div class="row">
            <div class="col-6">
                <input type="text" name="title" @if(isset($source)) value="{{ $source->title }}" @else value=""
                       @endif class="form-control">
                <label for="">{{ __('manager.traffic_sources.source-name') }}</label>
            </div>
            <div class="col-6">
                <button class="btn btn-primary" type="submit">{{ __('manager.traffic_sources.submit') }}</button>
            </div>
        </div>
    </form>
@endsection

