@extends('layouts.app')

@section('title', __('advertiser.offers.show.title') . ' ' . $offer->offer_name)

@section('content')
<div class="d-flex justify-content-between">
    <ul class="nav nav-tabs">
        <li class="nav-item" class="nav-item" role="presentation">
            <a href="#home1" class="nav-link active" data-bs-toggle="tab" role="tab" aria-controls="home1" aria-selected="true">
                {{ __('advertiser.offers.show.settings') }}
            </a>
        </li>
        @foreach($materials as $type => $items)
        <li class="nav-item" class="nav-item" role="presentation">
            <a href="#{{ $type }}" class="nav-link" data-bs-toggle="tab" role="tab" aria-controls="{{ $type }}" aria-selected="false">
                {{ $materials_types[$type] }}
            </a>
        </li>
        @endforeach
        <li class="nav-item" class="nav-item" role="presentation">
            <a href="#rateRules" class="nav-link" data-bs-toggle="tab" role="tab" aria-controls="rateRules" aria-selected="false">
                {{ __('advertiser.offers.show.fees') }}
            </a>
        </li>
    </ul>
    <div>
        <a href="{{ route("advertiser.servicedeskadv.create") }}?type=technical&subject=%D0%95%D1%81%D1%82%D1%8C%20%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%20%D0%BF%D0%BE%20%D0%BD%D0%B0%D1%81%D1%82%D1%80%D0%BE%D0%B9%D0%BA%D0%B0%D0%BC" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-question"></i>
            {{ __('advertiser.offers.show.question') }}
        </a>
    </div>
</div>

<div class="box border-top-0">
    <div class="box__content p-3">
        <div class="tab-content">
            <div class="tab-pane fade show active" id="home1">
                <form method="post" action="{{ route('advertiser.offers.update',$offer) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <x-input type="text" name="offer_name" :value="$offer->offer_name" placeholder="{{ __('advertiser.offers.show.offer_name.placeholder') }}" minlength="5" required autofocus>
                        {{ __('advertiser.offers.show.offer_name') }}
                        <x-slot name="help">{{ __('advertiser.offers.show.offer_name.desc') }}</x-slot>
                    </x-input>

                    <x-input type="textarea" name="description" :value="$offer->description" placeholder="{{ __('advertiser.offers.show.desc.placeholder') }}" minlength="15" required>
                        {{ __('advertiser.offers.show.desc') }}
                        <x-slot name="help">{{ __('advertiser.offers.show.desc.desc') }}
                        </x-slot>
                    </x-input>

                    <x-input type="select" name="model" :value="$offer->model" :options="App\Lists\OrderStateList::getList('V')" required>
                        {{ __('advertiser.offers.show.goal_action') }}
                        <x-slot name="help">{{ __('advertiser.offers.show.goal_action.desc') }}</x-slot>
                    </x-input>

                    <x-input type="select" id="fee_type" name="fee_type" :value="$offer->fee_type" :options="App\Lists\OffersFeeTypeList::getFeeTypeList()" onchange="change_fee()" required>
                        {{ __('advertiser.offers.show.fee_type') }}
                        <x-slot name="help">{{ __('advertiser.offers.show.fee_type.desc') }}</x-slot>
                    </x-input>
                    <script>
                        function change_fee() {
                                var fee_type = document.getElementById("fee_type").value;
                                if (fee_type === 'fix') {
                                    document.getElementById("fee_label").innerText = "{{ __('advertiser.offers.show.js.fee_fix') }} ({{ auth()->user()->pp->currency }})";
                                } else if (fee_type === 'share') {
                                    document.getElementById("fee_label").innerText = "{{ __('advertiser.offers.show.js.fee_share') }} (%)";
                                }
                            }
                    </script>

                    <x-input type="file" name="image" :value="$offer->image">
                        {{ __('advertiser.offers.show.logo') }}
                        <x-slot name="help">{{ __('advertiser.offers.show.logo.desc') }}</x-slot>
                    </x-input>

                    @foreach(\App\Lists\OffersMetumList::getList() as $k=>$v)
                    <div class="card mb-3">
                        <div class="card-header">{{ $v["title"] }}</div>
                        <div class="card-body">
                            @include("advertiser.offers.offersmeta." . $v["type"], $v)
                        </div>
                    </div>
                    @endforeach

                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">{{ __('advertiser.offers.show.save') }}</button>
                    </div>
                </form>
            </div>

            @foreach($materials as $type => $items)
            <div class="tab-pane fade" id="{{ $type }}">
                <div class="d-flex justify-content-between mb-3">
                    <div></div>
                    <a class="btn btn-primary btn-sm"
                       href="{{ route("advertiser.offers.materials.new") }}?offer={{ $offer->id }}&type={{ $type }}">
                        <i class="far fa-plus-square"></i>
                        {{ __('advertiser.offers.show.add') }}
                        <span class="text-lowercase">{{ $materials_types[$type] }}</span></a>
                </div>

                @php
                    $format = [
                        'name' => '',
                        'parameters' => 'html',
                        'delete_button' => 'html',
                    ];
                @endphp

                <x-table :format="$format" :data="collect($materials[$type])">
                    <x-slot name="thead">
                        <tr>
                            <th>{{ __('advertiser.offers.show.name') }}</th>
                            <th>{{ __('advertiser.offers.show.params') }}</th>
                            <th></th>
                        </tr>
                    </x-slot>
                </x-table>
            </div>
            @endforeach

            <div class="tab-pane fade" id="rateRules">
                <div class="d-flex justify-content-between mb-3">
                    <div></div>
                    <a class="btn btn-primary btn-sm"
                       href="{{ route('advertiser.rateRule.create') }}?offer_id={{ $offer->id }}">
                        <i class="far fa-plus-square"></i> {{ __('advertiser.offers.show.add_fee') }}</a>
                </div>
                @php
                $format = [
                    'fee' => ($offer->fee_type == 'share') ? 'format.percentage' : 'format.fee',
                    'date_start' => 'format.date',
                    'date_end' => 'format.date',
                    'business_unit_string' => 'html',
                    'progressive_param_name' => 'html',
                    'progressive_value_txt' => 'string',
                    'partner_name' => 'string',
                    'link_name' => 'string',
                    'edit_button' => 'html',
                ];
                @endphp

                <x-table :format="$format" :data="$rateRules">
                    <x-slot name="thead">
                        <tr>
                            <th>{{ __('advertiser.offers.show.fee') }}</th>
                            <th>{{ __('advertiser.offers.show.date_start') }}</th>
                            <th>{{ __('advertiser.offers.show.date_end') }}</th>
                            <th>{{ __('advertiser.offers.show.business_unit_string') }}</th>
                            <th>{{ __('advertiser.offers.show.progressive_param') }}</th>
                            <th>{{ __('advertiser.offers.show.progressive_value') }}</th>
                            <th>{{ __('advertiser.offers.show.partner_name') }}</th>
                            <th>{{ __('advertiser.offers.show.link_name') }}</th>
                            <th></th>
                        </tr>
                    </x-slot>
                </x-table>
            </div>
        </div>
    </div>
</div>
@endsection
