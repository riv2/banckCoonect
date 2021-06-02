@extends('errors::illustrated-layout')

@section('title', __('Вы не авторизованы'))
@section('code', '401')
@section('message', __('Вы не авторизованы'))
@section('image')
<div style="background-image: url({{ asset('/images/errors/401.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
