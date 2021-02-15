Вам поступилая информация о неисправности {{getcong('site_name')}}:

<br>
<p><strong>Данные пользователя:</strong></p>

@if( !empty($user_id) )
<p> <strong>ID пользователя:</strong> {{ $user_id }}</p>
@endif
<p> <strong>ФИО:</strong> {{ $fio }} </p>
<p> <strong>телефон:</strong> {{ $phone }} </p>

@if( !empty($reason) )
    <p> <strong>Причина:</strong> {{ $reason }}</p>
@endif

<p> <strong>Сообщение:</strong> <br>
{{ $text }}
</p>