@extends('layouts.app')

@section('title', __('manager.servicedesk.templates.show.app-title'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manager.servicedesk.index') }}">{{ __('manager.servicedesk.templates.show.app-title') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('manager.servicedesk.templates.index') }}">{{ __('manager.servicedesk.templates.show.templates') }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('manager.servicedesk.templates.show.templates') }}</li>
        </ol>
    </nav>

    @includeWhen(session()->has('success') || $errors->any(), 'widgets.alerts')

    <h1>{{ $item->title }}</h1>
    <form action="{{ route('manager.servicedesk.templates.update', $item) }}" method="post">
        @csrf
        @method('PUT')

        <div class="form-label-group">
            <input type="text" name="title" value="{{ old('title', $item->title) }}" class="form-control" required>
            <label>{{ __('manager.servicedesk.templates.show.title') }}</label>
        </div>

        <div class="form-label-group">
            <textarea name="body" class="form-control" required>{{ old('body', $item->body) }}</textarea>
            <label>{{ __('manager.servicedesk.templates.show.body') }}</label>
        </div>

        <div class="form-label-group">
            <select class="custom-select" name="is_favorite">
                <option value="0" @if(old('is_favorite', $item->is_favorite) == '0') selected @endif>{{ __('manager.servicedesk.templates.show.no') }}</option>
                <option value="1" @if(old('is_favorite', $item->is_favorite) == '1') selected @endif>{{ __('manager.servicedesk.templates.show.yes') }}</option>
            </select>
            <label>{{ __('manager.servicedesk.templates.show.in-favorites') }}</label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-outline-primary">{{ __('manager.servicedesk.templates.show.submit') }}</button>
        </div>
    </form>

</div>
@endsection
