@extends('layouts.app')

@section('title', __('Получение ссылок'))

@section('content')

    <form method="post" action="{{ route("partner.links.store") }}">
        @csrf
        <input type="hidden" name="id" value="{{ $offer_id }}">
        <input type="hidden" name="offer_material_id"
               value="@isset($OfferMaterial){{ $OfferMaterial->offer_material_id }}@endisset">
        <div class="card">
            <div class="card-header">@isset($OfferMaterial){{ $OfferMaterial->name }}@endisset</div>
            <div class="card-body">
                <em> @isset($OfferMaterial){{ $OfferMaterial->material_params["link"] }}@endisset</em>
                <br>
                <br>
                @if(isset($allowedTrafficSources))
                    <x-input type="select" id="fee_type" name="fee_type" :value="$allowedTrafficSources"
                             :options="$allowedTrafficSources" required>
                        Источник трафика
                        <x-slot name="help">Укажите источник трафика</x-slot>
                    </x-input>
                    <button class="btn btn-primary">Создать ссылку</button>
                @else
                    <p>Внимание! У оффера нет разрешённых источников трафика!</p>
                @endif

            </div>
        </div>
        <br>
    </form>

@endsection
