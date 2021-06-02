@extends('layouts.app')

@section('title', __('manager.servicedesk.comments.edit.app-title'))

@section('content')

    <x-box>
        <x-slot name="title">{{ __('manager.servicedesk.comments.edit.comment') }} #{{ $comment->id }}</x-slot>

        <div class="row">
            <div class="col-md-10 mx-auto">
                <form
                    action="{{ route(auth()->user()->role . '.servicedesk.comments.update', ['servicedesk' => $comment->task, 'comment' => $comment]) }}"
                    method="post">
                    @csrf
                    @method('PUT')

                    <div class="form-label-group mb-3">
                        <textarea name="body" class="form-control"
                                  placeholder="{{ __('manager.servicedesk.comments.edit.comment') }}"
                                  rows="10">{{ old('body', $comment->body) }}</textarea>
                        <label>{{ __('manager.servicedesk.comments.edit.comment') }}</label>
                    </div>

                    <div class="form-group text-end mb-3">
                        <button type="submit"
                                class="btn  btn-sm btn-primary">{{ __('manager.servicedesk.comments.edit.submit') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </x-box>

@endsection
