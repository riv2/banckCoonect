@extends('layouts.app')

@section('title', __('partners.offers.link.title'))

@section('content')

    <form method="post" action="{{ route("partner.links.create") }}">
        @csrf
        <input type="hidden" name="id" value="{{ $offer->id }}">
        <input type="hidden" name="offer_material_id" value="@isset($item){{ $item->offer_material_id }}@endisset">
        <div class="card">
            <div class="card-header">@isset($item){{ $item->name }}@endisset</div>
            <div class="card-body">
                <em>Разрешенный домен: @isset($item){{ $item->material_params["link"] }}@endisset</em>
                <input type="url" name="link" class="form-control" value="" required
                       placeholder="Ссылка на произвольную страницу сайта">
                <button class="btn btn-primary">Создать ссылку</button>
            </div>
        </div>
        <br>
    </form>

@endsection
