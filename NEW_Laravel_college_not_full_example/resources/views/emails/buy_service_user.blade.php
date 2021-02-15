Вам поступилая информация о покупке услуги студентом {{getcong('site_name')}}:

<br>
<p><strong>Данные пользователя:</strong></p>
<p> <strong>ID пользователя:</strong> {{ $user_id ?? '' }}</p>
<p> <strong>ФИО:</strong> {{ $fio ?? '' }} </p>

<p><strong>Данные об услуге:</strong></p>
<p> <strong>Наименование:</strong> {{ $service_name ?? '' }} ({{ $service_code ?? '' }})</p>
<p> <strong>Стоимость:</strong> {{ $cost ?? '' }} </p>

<p> <strong>Дата:</strong> {{ $date ?? '' }}</p>

@if($enquireURL != false)
    <p><strong>Ссылка на справку:</strong> <a href="{{$enquireURL}}">{{ $enquireURL }}</a></p>
@endif