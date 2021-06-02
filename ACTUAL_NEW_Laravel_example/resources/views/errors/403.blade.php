@extends('errors::illustrated-layout')

@section('title', __('Доступ запрещен'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Доступ запрещен'))
@section('image')
<div style="background-image: url({{ asset('/images/errors/403.svg') }});" class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center"></div>
@endsection
