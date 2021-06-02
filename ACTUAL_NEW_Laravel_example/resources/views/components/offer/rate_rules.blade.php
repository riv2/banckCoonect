<strong>{{ __('offer.fee') }}:</strong>{{ ' ' }}
@if(count($rateRules) == 1)
    @foreach($rateRules as $rateRule)
        @include('components.offer.fee_string')
    @endforeach
@else
    <ul class="list-group list-group-flush">
        @foreach($rateRules as $rateRule)
            <li class="list-group-item">
                @if(!is_null($rateRule->business_unit_id))
                    <span class="text-dark">{{ $rateRule->category_name }}</span>{{ ' - ' }}
                @endif
                @include('components.offer.fee_string')
                @if($rateRule->progressive_param == 'amount')
                    {{ ' ' . __('offer.cash-flow') . ' ' . __('offer.from') . ' ' . $rateRule->progressive_value
                    . ' ' .  $currency }}
                @elseif($rateRule->progressive_param == 'orders')
                    {{ ' ' . __('offer.orders-R') . ' ' . __('offer.from') . ' ' . $rateRule->progressive_value
                    . ' ' . __('offer.amount') }}
                @endif
            </li>
        @endforeach
    </ul>
@endif
