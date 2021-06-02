@extends('layouts.app')

@section('title', __('Обратная связь'))

@section('content')
    <button class="btn btn-primary" onclick="$('#newForm').toggle()">Создать запрос</button>
    <form method="post" id="newForm" style="display:none" action="{{ route("partner.support.send") }}">
        @csrf
        <br> <br>
        <div class="form-group">
            <label for="">Тип вопроса</label>
        <select class="form-control" name="case_type">
            @foreach($cases as $k=>$v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
        </div>
        <div class="form-group">
            <label for="">Ваш вопрос</label>
            <textarea rows="7" name="case_description" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Отправить</button>
        </div>
    </form>
    <br> <br>
    <h2>Ваши заявки:</h2>
    @foreach($support as $item)
        <div class="row">
            <div class="col col-3">Тип: {{ $cases[$item->case_type] }}</div>
            <div class="col col-3">Создано: {{ $item->created_at }}</div>
            <div class="col col-6">Обновлено: {{ $item->updated_at }}</div>
            <div class="col col-6">
                <strong>Ваше сообщение</strong><br>
                {{ $item->case_description }}
            </div>
            <div class="col col-6">
                <strong>Ответ</strong><br>
                @if($item->answer)
                    {{ $item->answer }}
                @else
                    Ответ пока не получен
                @endif
            </div>
        </div>
        <div>
            <br> <br>
        </div>
    @endforeach
@endsection

