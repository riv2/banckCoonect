@extends('layouts.app')

@section('title', __('advertiser.registries.show.app-title'))

@section('content')

<x-box>
    <a href="{{ route("advertiser.reestrs.index") }}">{{ __('advertiser.registries.show.app-title') }}</a>
</x-box>
<x-box>
    <div class="table-responsive">
    	<table class="table table-hover">
    		<thead>
    			<tr>
    				<th>ID</th>
    				<th>{{ __('advertiser.registries.show.fee') }}</th>
    				<th>{{ __('advertiser.registries.show.partner') }}</th>
    				<th>{{ __('advertiser.registries.show.status') }}</th>
    			</tr>
    		</thead>
    		<tbody>
            @foreach($reestr->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_id }}</td>
                    <td>{{ $payment->revenue }}</td>
                    <td>{{ $payment->partner_id }}</td>
                    <td>{{ $payment->status }}</td>
                </tr>
            @endforeach
    		</tbody>
    	</table>
    </div>

</x-box>

@endsection
