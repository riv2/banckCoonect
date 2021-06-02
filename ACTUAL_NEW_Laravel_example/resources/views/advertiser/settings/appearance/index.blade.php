@extends('layouts.app')

@section('title', __('advertiser.settings.appearance.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.settings.appearance.title') }}</x-slot>

        <form action="{{ route('advertiser.settings.appearance.update') }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.short-name') }}</label>
                        <input type="text" name="short_name"
                               class="form-control @error('short_name') is-invalid @enderror"
                               value="{{ old('short_name', $pp->short_name) }}">
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.long-name') }}</label>
                        <input type="text" name="long_name"
                               class="form-control @error('long_name') is-invalid @enderror"
                               value="{{ old('long_name', $pp->long_name) }}">
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.language') }}</label>
                        <div class="pt-1">
                            @foreach(config('app.locales') as $key => $item)
                                <input type="checkbox" name="lang[]" id="{{ $key }}" value="{{ $key }}"
                                       @if($pp->lang[$key]) checked @endif/>
                                <span class="m-1 text-uppercase">{{ $key }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6 small">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.colour-1') }}</label>
                        <input type="text" name="color1" class="form-control @error('color1') is-invalid @enderror"
                               value="{{ old('color1', $pp->color1) }}">
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.colour-2') }}</label>
                        <input type="text" name="color2" class="form-control @error('color2') is-invalid @enderror"
                               placeholder="{{ __('advertiser.settings.appearance.colour-2-placeholder') }}"
                               value="{{ old('color2', $pp->color2) }}">
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.colour-3') }}</label>
                        <input type="text" name="color3" class="form-control @error('color3') is-invalid @enderror"
                               value="{{ old('color3', $pp->color3) }}">
                    </div>
                    <div class="form-label-group mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.colour-4') }}</label>
                        <input type="text" name="color4" class="form-control @error('color4') is-invalid @enderror"
                               value="{{ old('color4', $pp->color4) }}">
                    </div>
                </div>
                <div class="col-md-6 small">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.logo-download') }}</label>
                        @if ($pp->logo)
                            <div class="mb-3">
                                <img src="{{ $pp->logo }}" alt="" class="img-fluid img-thumbnail w-50">
                            </div>
                        @endif
                        <div class="mb-3">
                            <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror"
                                   id="input_upload_logo">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 small">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('advertiser.settings.appearance.favicon-download') }}</label>
                        @if ($pp->favicon)
                            <div class="mb-3">
                                <img src="{{ $pp->favicon }}" alt="" class="img-fluid img-thumbnail w-50">
                            </div>
                        @endif
                        <div class="mb-3">
                            <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror"
                                   id="input_upload_favicon">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 small">
                </div>
            </div>

            <div class="mb-5">
                <button class="btn btn-primary" type="submit">{{ __('advertiser.settings.appearance.submit') }}</button>
            </div>

        </form>
    </x-box>
@endsection
