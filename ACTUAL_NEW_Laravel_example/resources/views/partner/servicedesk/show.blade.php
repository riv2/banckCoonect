@extends('layouts.app')

@section('title', 'Тикет #'. $item->id )

@section('content')

    <x-box>
        <x-slot name="title">Тема: {{ $item->subject }}</x-slot>

        <div class="row mb-5">
            <div class="col-md-10 offset-md-1">
                <div class="row row-cols-2">
                    <div class="col">
                        <div>
                            Тип обращения:
                            <span class="{{ $item->type_class }}">
                            {{ $item->type_text }}
                        </span>
                        </div>
                        <div>
                            Статус:
                            <span class="{{ $item->status_class }}">
                            {{ $item->status_text }}
                        </span>
                        </div>
                    </div>
                    <div class="col text-end">
                        @if ($item->created_at)
                            <div>
                                Дата создания:
                                {{ Date::parse($item->created_at)->format('j F Y H:i:s') }}
                            </div>
                        @endif
                        @if ($item->deadline_at)
                            <div>
                                Срок ответа:
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
                            <form action="{{ route('partner.servicedesk.update', $item) }}" method="post" class="text-center my-5">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="status" value="pending">
                                <button type="submit" class="btn btn-primary">Мое обращение не решено</button>
                            </form>
                        @endif
                        <form action="{{ route('partner.servicedesk.update', $item) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="comment">
                            <div class="form-label-group">
                                <textarea name="body" class="form-control" rows="4" required>{{ old('body') }}</textarea>
                                @if ($item->status !== 'closed')
                                    <label>Написать комментарий</label>
                                @else
                                    <label>Написать отзыв</label>
                                @endif
                            </div>
                            <div class="form-group mt-3">
                                <input type="file" name="attach" class="form-control" id="commentattach" multiple>
                                <small class="form-text text-muted">
                                    Разрешенные форматы: jpg,png,doc,docx,xls,xlsx<br>
                                    Максимальный размер вложения - 2 мегабайта
                                </small>
                            </div>
                            <br>
                            <div class="form-label-group mb-3">
                                <select name="status" class="form-select">
                                    @foreach(\App\Models\ServicedeskTask::$statuses as $status => $status_text)
                                        @if($status != 'new')
                                            <option @if($item->status == $status) selected @endif value="{{ $status }}">
                                                {{$status_text['caption'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <label>{{ __('manager.servicedesk.show.ticket-status') }}</label>
                            </div>
                            <div class="form-group text-md-right">
                                <button type="submit" class="btn btn-sm btn-primary">Отправить</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-1"></div>
                </div>
    </x-box>

@endsection
