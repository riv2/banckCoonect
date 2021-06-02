@extends('layouts.app')

@section('title', __('advertiser.offersmaterials.new.app-title'))

@section('content')
    <a href="{{ route("advertiser.offers.show", $offer)}}?offer={{$offer}} ">
        {{ __('advertiser.offersmaterials.new.right-button') }}</a>
    <br>
    <br>
    <form id="selecttype">
        <input type="hidden" name="offer" value="{{ $offer }}">
        <select class="form-control" name="type" onchange="$('#selecttype').submit()" required>
            <option value="">{{ __('advertiser.offersmaterials.new.select-default-option') }}</option>
            @foreach(\App\Lists\OffersMaterialsTypesList::getList() as $k=>$v)
                <option
                    @if($k == ($type)) selected @endif
                value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
    </form>
    <div>&nbsp;</div>
    <div class="card">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="offer" value="{{ $offer->id }}">
                @switch($type)
                    @case ("link")
                    @include("advertiser.offersmaterials.material-link")
                    @break
                    @case ("landing")
                    @include("advertiser.offersmaterials.material-landing")
                    @break
                    @case ("banner")
                    @include("advertiser.offersmaterials.material-banner")
                    @break
                    @case ("xmlfeed")
                    @include("advertiser.offersmaterials.material-xmlfeed")
                    @break
                    @case ("pwa")
                    @include("advertiser.offersmaterials.material-pwa")
                    @break
                    @default
                    {{ __('advertiser.offersmaterials.new.switch-default-option') }}
                @endswitch
            </form>
        </div>
    </div>
@endsection

