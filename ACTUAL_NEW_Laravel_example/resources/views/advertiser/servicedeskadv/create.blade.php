@extends('layouts.app')

@section('title', __('advertiser.servicedeskadv.create.app-title'))

@section('content')
    <x-box>
        <x-slot name="title">{{ __('advertiser.servicedeskadv.create.title') }}</x-slot>
        <form method="post" action="{{ route('advertiser.servicedeskadv.store') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <x-input type="select" name="type" :value="request()->get('type') ?? ''" required data-nullvalue
                     :options="\App\Models\ServicedeskTask::getTaskTypesList()" autofocus>
                {{ __('advertiser.servicedeskadv.create.request-type') }}
                <x-slot name="help">{{ __('advertiser.servicedeskadv.create.choose-section') }}</x-slot>
            </x-input>

            <x-input type="text" name="subject" :value="request()->query('subject') ?? ''"
                     placeholder="{{ __('advertiser.servicedeskadv.create.request-theme-placeholder') }}?" required>
                {{ __('advertiser.servicedeskadv.create.request-theme') }}
                <x-slot name="help">{{ __('advertiser.servicedeskadv.create.request-theme-help') }}</x-slot>
            </x-input>

            <x-input type="textarea" name="body"
                     placeholder="{{ __('advertiser.servicedeskadv.create.body-placeholder') }}" required>
                {{ __('advertiser.servicedeskadv.create.body') }}
                <x-slot name="help">{{ __('advertiser.servicedeskadv.create.body-desc') }}</x-slot>
            </x-input>

            <x-input type="file" name="attach">
                {{ __('advertiser.servicedeskadv.create.attach') }}
                <x-slot name="help">{{ __('advertiser.servicedeskadv.create.attach-desc') }}</x-slot>
            </x-input>

            <div class="mb-3">
                <button type="submit"
                        class="btn btn-primary">{{ __('advertiser.servicedeskadv.create.submit') }}</button>
            </div>
        </form>
    </x-box>
@endsection
