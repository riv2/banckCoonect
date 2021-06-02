@extends('layouts.app')

@section('title', __('advertiser.servicedesk.comments.edit.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('advertiser.servicedesk.comments.edit.title') }} #{{ $comment->id }}</x-slot>

        <div class="row">
            <div class="col-md-10 mx-auto">
                <form action="{{ route(auth()->user()->role . '.servicedesk.comments.update', ['servicedesk' => $comment->task, 'comment' => $comment]) }}" method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-label-group mb-3">
                        <textarea name="body" class="form-control" placeholder="{{ __('advertiser.servicedesk.comments.edit.comment') }}" rows="10">{{ old('body', $comment->body) }}</textarea>
                        <label>{{ __('advertiser.servicedesk.comments.edit.comment') }}</label>
                    </div>

                    <div class="form-group text-end mb-3">
                        <button type="submit" class="btn  btn-sm btn-primary">{{ __('advertiser.servicedesk.comments.edit.title') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </x-box>
@endsection
