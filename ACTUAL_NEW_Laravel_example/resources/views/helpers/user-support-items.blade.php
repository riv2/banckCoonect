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
