<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>
<p>Уважаемый {{ $user->studentProfile->fio }}!</p>
<p>Ваши документы проверены. По результатам данных ЕНТ Вам будет предложена траектория {{ $firstSpeciality->name }} на 1 семестр обучения с дальнейшим переводом на специальность {{ $secondSpeciality->name }}.</p>
<p>
    Для формирования личного дела необходимо:
<ol>
    <li>Оплатить вступительный взнос 2000 тг</li>
    <li>Загрузить сканированные копии документов в Профиль</li>
    <li>Предоставить оригиналы документов в срок до 25 августа текущего года по адресу:
        г Шымкент, ул Гани Иляева 3 (почтовый индекс X15C9A5), Офис Приемной комиссии Miras Education.
    </li>
</ol>
</p>

@if($user->studentProfile->speciality->code_char == \App\Speciality::CODE_CHAR_BACHELOR)
    <p>
        Перечень необходимых документов для поступления в бакалавриат:
    <ol>
        <li>Документ об общем среднем, техническом и профессиональном, послесреднем или высшем образовании с приложением (подлинник);</li>
        <li>Медицинская справка формы 086-У (обязательно фотография, печать и снимок флюорографии не позднее текущего года), 063-прививки;</li>
        <li>Сертификат ЕНТ (оригинал);</li>
        <li>Подписанный договор на обучение (оригинал);</li>
        <li>Заявление на прием с подписью студента (Скачать форму заявления)</li>
    </ol>
    </p>
@endif

</body>
</html>