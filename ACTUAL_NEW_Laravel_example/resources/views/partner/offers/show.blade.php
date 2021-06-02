@extends('layouts.app')

@section('title', __('partners.offers.show.title') . ' ' . $offer->offer_name)

@section('content')
    <div class="d-flex justify-content-between">
        <ul class="nav nav-tabs">
            <li class="nav-item" class="nav-item" role="presentation">
                <a href="#home1" class="nav-link active" data-bs-toggle="tab" role="tab" aria-controls="home1"
                   aria-selected="true">
                    {{ __('partners.offers.show.desc') }}
                </a>
            </li>
            @foreach($materials as $type => $items)
                <li class="nav-item" class="nav-item" role="presentation">
                    <a href="#{{ $type }}" class="nav-link" data-bs-toggle="tab" role="tab"
                       aria-controls="{{ $type }}"
                       aria-selected="false">
                        {{ $materials_types[$type] }}
                    </a>
                </li>
            @endforeach
        </ul>
        <div>
        </div>
    </div>

    <div class="box border-top-0">
        <div class="box__content p-3">
            <div class="tab-content">
                <div class="tab-pane fade show active" id="home1">
                    <div class="row">
                        <div class="col-12 col-md-9">
                            <h1>{{ $offer->offer_name }}</h1>
                            <div class="mb-3">
                                {!! $offer->description !!}
                            </div>

                            <div class="mb-3">
                                <strong>{{ __('partners.offers.show.goal_action') }}:</strong>
                                <span id="goal_action">{{App\Lists\OrderStateList::getList()[$offer->model]}}</span>
                            </div>
                            <div class="mb-3">
                                {!! $offer->rate_rules !!}
                            </div>
                            @isset($item)
                                {!! $offer->landing_link !!}
                            @endisset
                        </div>
                        <div class="col-12 col-md-3">
                            <img src="{{$offer->image}}" alt="{{ $offer->offer_name }}"
                                 class="img-fluid img-thumbnail my-2">
                        </div>
                    </div>
                </div>

                @foreach($materials as $type => $items)
                    <div class="tab-pane fade" id="{{ $type }}">
                        @foreach($materials[$type] as $item)
                            <div class="card mb-3">
                                <div class="card-header">
                                    {{ $item->name }}
                                </div>
                                <div class="card-body">
                                    @include('partner.offers.materials.' . $item->material_type)
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
