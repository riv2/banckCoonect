@extends('layouts.app')

@section('title', __('Новый тикет'))

@section('content')
    <x-box>
        <x-slot name="title">Новый тикет</x-slot>
        <form method="post" action="{{ route('partner.servicedesk.store') }}" enctype="multipart/form-data">
            @csrf
            @method('POST')

            <x-input type="select" name="type" required data-nullvalue :options="\App\Models\ServicedeskTask::getTaskTypesList()" autofocus>
                Тип запроса
                <x-slot name="help">Выберите к какому разделу относится запрос</x-slot>
            </x-input>

            <x-input type="text" name="subject" placeholder="Где найти инструкцию?" required>
                Тема запроса
                <x-slot name="help">Кратко опишите проблему</x-slot>
            </x-input>

            <x-input type="textarea" name="body" placeholder="Не разобрался как получить ссылку :(" required>
                Описание проблемы
                <x-slot name="help">Развёрнуто опишите суть проблемы</x-slot>
            </x-input>

            <x-input type="file" name="attach">
                Прикрепить файл
                <x-slot name="help">Разрешенные форматы: jpg,png,doc,docx,xls,xlsx. Максимальный размер вложения - 2 мегабайта</x-slot>
            </x-input>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary">Отправить</button>
            </div>
        </form>
    </x-box>
@endsection
