@extends('layouts.app')

@section('title', __('manager.servicedesk.templates.create.app-title'))

@section('content')
<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('manager.servicedesk.index') }}">{{ __('manager.servicedesk.templates.create.app-title') }}</a>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('manager.servicedesk.templates.index') }}">{{ __('manager.servicedesk.templates.create.templates') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('manager.servicedesk.templates.create.new-templates') }}</li>
        </ol>
    </nav>

    @includeWhen(session()->has('success') || $errors->any(), 'widgets.alerts')

    <h1>{{ __('manager.servicedesk.templates.create.new-record') }}</h1>
    <form action="{{ route('manager.servicedesk.templates.store') }}" method="post">
        @csrf

        <div class="form-label-group">
            <input type="text" name="title" value="{{ old('title') }}" class="form-control" required>
            <label>{{ __('manager.servicedesk.templates.create.title') }}</label>
        </div>

        <div class="form-label-group">
            <textarea name="body" class="form-control" required>{{ old('body') }}</textarea>
            <label>{{ __('manager.servicedesk.templates.create.body') }}</label>
        </div>

        <div class="form-label-group">
            <select class="custom-select" name="is_favorite">
                <option value="0" @if(old('is_favorite')=='0' ) selected @endif>{{ __('manager.servicedesk.templates.create.no') }}</option>
                <option value="1" @if(old('is_favorite')=='1' ) selected @endif>{{ __('manager.servicedesk.templates.create.yes') }}</option>
            </select>
            <label>{{ __('manager.servicedesk.templates.create.in-favorites') }}</label>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-outline-primary">{{ __('manager.servicedesk.templates.create.creates') }}</button>
        </div>
    </form>

</div>
@endsection
