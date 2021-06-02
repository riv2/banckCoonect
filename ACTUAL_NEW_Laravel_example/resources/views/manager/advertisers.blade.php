@extends('layouts.app')

@section('title', __('Рекламодатели'))

@section('content')
    @include("helpers.filter")

    <x-box>
        <x-slot name="title">Список рекламодателей</x-slot>
        <x-slot name="rightblock"></x-slot>

        @php
            $format = [
                'id' => 'format.string',
                'view_link' => 'html',
                'pp_domain' => 'format.string',
                'pp_id' => 'format.string',
                'created_at' => 'format.datetime',
                'name' => 'format.string',
                'onboarding_status_text' => 'format.string',
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
                    <th>Статус онбординга</th>
                    <th>Авторизоваться</th>
                </tr>
            </x-slot>
            <x-slot name="empty">
                Ни одной записи не найдено!
            </x-slot>
        </x-table>
    </x-box>
@endsection
