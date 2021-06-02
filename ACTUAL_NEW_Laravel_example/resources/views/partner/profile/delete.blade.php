@extends('layouts.app')

@section('title', __('Профиль'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="border rounded p-4">
                <h1>Удалить профиль?</h1>
                <p>Вы действительно хотите удалить свой аккаунт?</p>
                <p>Это действие необратимо!</p>
                <div class="row">
                    <div class="col-sm-8">
                        <a href="/" class="btn btn-primary btn-block">Нет</a>
                    </div>
                    <div class="col-sm-4">
                        <form action="/partner/profile/delete" method="post">
                            @csrf
                            <button class="btn btn-outline-secondary btn-block">Да</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
