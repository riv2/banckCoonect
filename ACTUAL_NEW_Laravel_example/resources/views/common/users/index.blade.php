@extends('layouts.app')

@section('title', __($page_title))

@section('content')
    @include("helpers.filter")

    <x-box>
        <x-slot name="title">{{ $page_title }}</x-slot>
        <x-slot name="rightblock">
            @if (Route::has(auth()->user()->role . '.' . Str::plural($role) . '.create'))
            <a href="{{ route(auth()->user()->role . '.' . Str::plural($role) . '.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="far fa-plus-square"></i> Добавить
            </a>
            @endif
        </x-slot>

        @php
            $format = [
                'id' => 'format.string',
                'view_link' => 'html',
                'pp_domain' => 'format.string',
                'pp_id' => 'format.string',
                'created_at' => 'format.datetime',
                'name' => 'format.string',
                'impersonate_link_button' => 'html',
            ];
        @endphp
        <x-table :data="$partners" :format="$format">
            <x-slot name="thead">
                <tr>
                    <th>USER ID</th>
                    <th>Email</th>
                    <th>Домен</th>
                    <th>PP ID</th>
                    <th>Дата и время регистрации</th>
                    <th>Имя</th>
                    <th>Авторизоваться</th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                Ни одной записи не найдено!
            </x-slot>
        </x-table>
    </x-box>
@endsection
