Вам поступилая информация о покупке курса студентом {{getcong('site_name')}}:

<br>
<p><strong>Данные пользователя:</strong></p>
<p> <strong>ID пользователя:</strong> {{ $user_id ?? '' }}</p>
<p> <strong>ФИО:</strong> {{ $fio ?? '' }} </p>
@if( !empty($speciality) )
<p> <strong>Специальность:</strong> {{ $speciality ?? '' }} ({{ $speciality_id ?? '' }}) </p>
@endif
@if( !empty($course) )
    <p> <strong>Курс:</strong> {{ $course ?? '' }} </p>
@endif

<p><strong>Данные об курсе:</strong></p>
<p> <strong>Наименование:</strong> {{ $service_name ?? '' }} ({{ $service_code ?? '' }})</p>
<p> <strong>Стоимость:</strong> {{ $cost ?? '' }} </p>

<p> <strong>Дата:</strong> {{ $date ?? '' }}</p>
