@extends('emails.layout.message')

@section('content')
    После того, как интеграция настроена и проверена, пора приглашать партнёров зарегистрироваться в вашей программе
    <a href="https://{{ $user->tech_domain }}/register">по ссылке</a> или добавлять их самостоятельно в интерфейсе.

    <a href="https://{{ $user->tech_domain }}/advertiser/partners" class="btn btn-primary">Перейти в раздел с партнёрами</a>

    Чтобы вам было легче, мы подготовили целую статью с советами о том,
    <a href="https://gocpa.ru/blog/searching-for-partners">как искать партнёров в свою партнёрскую программу</a>

    Посмотрите нашу <a href="https://gocpa.ru/gocpa_starting">короткую видеоинструкцию</a>, чтобы быстрее начать.
@endsection
