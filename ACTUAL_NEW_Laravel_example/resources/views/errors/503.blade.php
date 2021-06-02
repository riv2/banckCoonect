@extends('errors::illustrated-layout')

@section('title', __('Сервис временно недоступен'))
@section('code', $code ?? '503')
@section('message', __($exception->getMessage() ?: 'Попробуйте обновить страницу через несколько минут'))
@section('image')
<div style="background-image: url({{ asset('/images/errors/503.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
