@extends('layouts.app')

@section('title')
    {{ __('manager.servicedesk.show.app-title') . '#' . $item->id }}
@endsection

@section('content')

    <x-box>
        <x-slot name="title">{{ __('manager.servicedesk.show.title') . ':' . $item->subject }}</x-slot>

        <div class="row mb-5">
            <div class="col-md-10 offset-md-1">
                <div class="row row-cols-2">
                    <div class="col">
                        <div>
                            {{ __('manager.servicedesk.show.request-type') }}:
                            <span class="{{ $item->type_class }}">
                            {{ $item->type_text }}
                        </span>
                        </div>
                        <div>
                            {{ __('manager.servicedesk.show.status') }}:
                            <span class="{{ $item->status_class }}">
                            {{ $item->status_text }}
                        </span>
                        </div>
                    </div>
                    <div class="col text-end">
                        @if ($item->created_at)
                            <div>
                                {{ __('manager.servicedesk.show.created-at') }}:
                                {{ Date::parse($item->created_at)->format('j F Y H:i:s') }}
                            </div>
                        @endif
                        @if ($item->deadline_at)
                            <div>
                                {{ __('manager.servicedesk.show.deadline-at') }}:
                                {{ Date::parse($item->deadline_at)->format('j F Y H:i:s') }}
                            </div>
                        @endif
                    </div>
                </div>
                <hr>
            </div>
            <div class="col-md-1"></div>
        </div>


        @foreach ($item->comments as $comment)
            <x-servicedesk.comment :comment="$comment"></x-servicedesk>
                @endforeach

                <div class="row mb-2">
                    <div class="col-md-10 offset-md-1">
                        @if ($item->status === 'closed')
                            <form action="{{ route('manager.servicedesk.update', $item) }}" method="post"
                                  class="text-center my-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="status" value="pending">
                                <button type="submit"
                                        class="btn btn-primary">{{ __('manager.servicedesk.show.reopen') }}</button>
                            </form>
                        @endif
                        <form action="{{ route('manager.servicedesk.update', $item) }}" method="post"
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="comment">

                            <div class="form-label-group mb-3">
                                <select class="form-select js-comment-template">
                                    <option value="">{{ __('manager.servicedesk.show.choose-template') }}</option>
                                    @foreach(\App\Models\ServicedeskTaskTemplate::orderBy('is_favorite', 'desc')->get() as $template)
                                        <option value="{{ $template->id }}"
                                                data-value='@json($template->body, JSON_UNESCAPED_UNICODE)'>
                                            @if ($template->is_favorite)
                                                *
                                            @endif
                                            {{ $template->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <label>{{ __('manager.servicedesk.show.choose-template') }}</label>
                            </div>

                            <div class="form-label-group mb-3">
                                <textarea name="body" class="form-control" rows="4"
                                          required>{{ old('body') }}</textarea>
                                @if ($item->status !== 'closed')
                                    <label>{{ __('manager.servicedesk.show.write-a-comment') }}</label>
                                @else
                                    <label>{{ __('manager.servicedesk.show.write-a-review') }}</label>
                                @endif
                            </div>

                            <div class="form-group mb-3">
                                <input type="file" name="attach[]" class="form-control" id="commentattach" multiple>
                                <small class="form-text text-muted">
                                    {{ __('manager.servicedesk.show.allowed-formats') }}: jpg,png,doc,docx,xls,xlsx<br>
                                    {{ __('manager.servicedesk.show.max-size') }}
                                </small>
                            </div>

                            <div class="form-label-group mb-3">
                                <select name="status" class="form-select">
                                    @foreach(\App\Models\ServicedeskTask::$statuses as $status => $status_text)
                                        @if($status != 'new')
                                            <option @if($item->status == $status) selected @endif value="{{ $status }}">
                                                {{ $status_text['caption'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>{{ __('manager.servicedesk.show.ticket-status') }}</label>
                            </div>

                            <div class="form-group text-md-right">
                                <button type="submit"
                                        class="btn btn-sm btn-primary">{{ __('manager.servicedesk.show.send') }}</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1"></div>
                </div>
    </x-box>

@endsection
