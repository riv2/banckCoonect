@extends('errors::illustrated-layout')

@section('title', __('Ошибка сервера'))
@section('code', '500')
@section('message', __('Мы уже работаем над её исправлением'))
@section('image')
<div style="background-image: url({{ asset('/images/errors/500.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
