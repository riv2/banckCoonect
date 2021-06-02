@extends('layouts.app')

@section('title', __('advertiser.banned_links.create.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.banned_links.create.title') }}</x-slot>
        <x-slot name="rightblock"></x-slot>
        <form action="{{ route("advertiser.banned-links.store") }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.link-id') }}</label>
                        <input type="text" name="link_id"
                               class="form-control @error('link_id') is-invalid @enderror"
                               value="{{ old('link_id') }}" min="5" required>
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_links.create.link-id-desc') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.web-id') }}</label>
                        <input type="text" name="web_id"
                               class="form-control @error('web_id') is-invalid @enderror"
                               value="{{ old('web_id') }}" min="5">
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_links.create.web-id-desc') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.date-start') }}</label>
                        <input type="date" name="date_start"
                               class="form-control @error('date_start') is-invalid @enderror"
                               value="{{ old('date_start') ?? date('Y-m-d', strtotime('now')) }}" min="5" required>
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_links.create.date-start-desc') }}</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.date-end') }}</label>
                        <input type="date" name="date_end"
                               class="form-control @error('date_end') is-invalid @enderror"
                               value="{{ old('date_end') }}" min="5">
                    </div>
                </div>
                <div class="col-md-4 small align-self-center">
                    <p>{{ __('advertiser.banned_links.create.date-end-desc') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.comment') }}</label>
                        <textarea name="comment" rows="4"
                                  class="form-control js-wysiwyg @error('comment') is-invalid @enderror"
                                  required>{{ old('comment') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 small align-self-top">
                    <p>{{ __('advertiser.banned_links.create.comment-desc') }}</p>
                    <p>{{ __('advertiser.banned_links.create.comment-desc1') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.banned_links.create.evidence') }}</label>
                        <textarea name="evidence" rows="4"
                                  class="form-control js-wysiwyg @error('evidence') is-invalid @enderror"
                                  required>{{ old('evidence') }}</textarea>
                    </div>
                </div>
                <div class="col-md-4 small align-self-top">
                    <p>{{ __('advertiser.banned_links.create.evidence-desc') }}</p>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.banned_links.create.send') }}</button>
            </div>
        </form>
    </x-box>
@endsection
