@extends('layouts.app')

@section('title', __('advertiser.offers.edit.title'))

@section('content')
    <form method="post"  id="editform" enctype="multipart/form-data" action="{{ route("advertiser.offers.update") }}"> <br> <br>
        @csrf
        <input type="hidden" name="offer" value="{{ request("offer") }}">
        <div class="row">
            <div class="col col-8">
                <div class="form-group">
                    <label for="">{{ __('advertiser.offers.edit.offer_name') }}</label>
                    <input type="text" required name="offer_name" value="{{ $offer->offer_name }}" class="form-control">
                </div>
            </div>
            <div class="col col-4">
                <div class="form-group">
                    <label for="">{{ __('advertiser.offers.edit.img') }}</label>
                    <input type="file" class="form-control" name="file">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-3">
                <div class="form-group">
                    <label for="">{{ __('advertiser.offers.edit.fee') }}</label>
                    <input type="number" required name="fee_advert" value="
{{--                        {{ $offer->fee_advert }}--}}
                        " class="form-control" min="0.0001" step="0.0001" readonly>
                </div>
            </div>
            <div class="col col-3">
                <div class="form-group">
                    <label for="">{{ __('advertiser.offers.edit.model') }}</label>
                    <select name="model" class="form-control">
                        @foreach(\App\Lists\OrderStateList::getList() as $k=>$v)
                            <option
                                @if ($k == $offer->model) selected @endif
                                value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="">{{ __('advertiser.offers.edit.description') }}</label>
            <textarea name="description" class="form-control">{{ $offer->description }}</textarea>
        </div>

        <h3>{{ __('advertiser.offers.edit.settings') }}</h3>
        @foreach(\App\Lists\OffersMetumList::getList() as $k=>$v)
            <div class="card">
                <div class="card-header">{{ $v["title"] }}</div>
                <div class="card-body">
                    @include("advertiser.offers.offersmeta." . $v["type"], $v)
                </div>
            </div>
        @endforeach

        <br> <br>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">{{ __('advertiser.offers.edit.save') }}</button>
        </div>
    </form>
@endsection
