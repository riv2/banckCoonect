@extends('layouts.app')

@section('title', __('advertiser.registries.index.app-title'))

@section('content')

<x-box>
    <a href="{{ route("advertiser.reestrs.create") }}">{{ __('advertiser.registries.index.create-new-register') }}</a>
</x-box>
<x-box>
    <x-slot name="title">{{ __('advertiser.registries.index.title') }}</x-slot>
    <x-box>
        {{ $collection->links() }}
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ __('advertiser.registries.index.fee') }}</th>
                    <th>{{ __('advertiser.registries.index.data') }}</th>
                    <th>{{ __('advertiser.registries.index.total') }}</th>
                    <th>{{ __('advertiser.registries.index.payed') }}</th>
                    <th>{{ __('advertiser.registries.index.status') }}</th>
                    <th> </th>
                </tr>
                </thead>
                <tbody>
                @foreach($collection as $reestr)
                    <tr>
                        <td><a href="{{ route("advertiser.reestrs.show", $reestr) }}">{{ $reestr->reestr_id }}</a></td>
                        <td> </td>
                        <td>{{ $reestr->datetime }}</td>
                        <td>{{ $reestr->total }}</td>
                        <td>{{ $reestr->payed }}</td>
                        <td>{{ $reestr->status }}</td>
                        <td> </td>
                        <td>
                            <a href="{{ route("advertiser.reestrs.export", $reestr->reestr_id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="far fa-file-excel"></i> {{ __('advertiser.registries.index.export') }}
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </x-box>
</x-box>

@endsection
