{{ $rateRule->fee }}
@if($feeType == 'fix')
    {{ ' ' . $currency }}
@else
    {{ '%' }}
@endif
{{ ' ' . __('offer.for') . ' ' }}<span class="text-lowercase">{{ $orderState }}</span>
