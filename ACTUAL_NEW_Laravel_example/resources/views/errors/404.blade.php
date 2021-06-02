@extends('errors::illustrated-layout')

@section('title', __('Страница не найдена'))
@section('code', '404')
@section('message', __($exception->getMessage() ?: 'Страница не найдена'))
@section('image')
<div style="background-image: url({{ asset('/images/errors/404.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
