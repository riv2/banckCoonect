@extends('layouts.app')

@section('title', __('advertiser.offersmaterials.edit.app-title'))

@section('content')
    <div>&nbsp;</div>
    <div class="card">
    <div class="card-body">
        <form method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="offer_material_id" value="{{ $item->offer_material_id }}">
    @switch($item->material_type)
        @case ("link")
        @include("advertiser.offersmaterials.material-link", ["item"=>$item])
        @break
        @case ("landing")
        @include("advertiser.offersmaterials.material-landing", ["item"=>$item])
        @break
        @case ("banner")
        @include("advertiser.offersmaterials.material-banner", ["item"=>$item])
        @break
        @case ("xmlfeed")
        @include("advertiser.offersmaterials.material-xmlfeed", ["item"=>$item])
        @break
        @case ("pwa")
        @include("advertiser.offersmaterials.material-pwa", ["item"=>$item])
        @break
        @default
    @endswitch
        </form>
    </div>
    </div>
@endsection
