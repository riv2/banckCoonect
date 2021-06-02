@extends('layouts.app')

@section('title', __('advertiser.registries.index.app-title'))

@section('content')

    <x-box>


        <main class="maincontainer">
            <div class="container">
                <h1>{{ __('advertiser.registries.index.title') }}</h1>

                <div class="row mb-2">
                    <div class="col-lg-8 mx-auto">
                        <div class="bg-light border p-4">
                            <h5 class="text-center">{{ __('advertiser.registries.create.create-new-reg') }}</h5>

                            <form action="{{ route("advertiser.reestrs.store") }}" method="post"
                                  name="new_reestr" onchange="count_orders()">
                                @csrf

                                <div class="form-group row mb-2">
                                    <label class="col-md-3 col-form-label text-md-right">Заявки по:</label>
                                    <div class="col-md-9">
                                        <input type="date" name="date_end" value="2021-03-31" class="form-control"
                                               required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-md-9 offset-md-3">
                                        <button class="btn btn-primary">{{ __('advertiser.registries.index.create') }}</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>


                <p class="text-right mb-2">
                    <span class="table-span-warning d-inline-block p-1">Цветом выделены еще не оплаченные реестры</span>
                </p>

                {{ $collection->links() }}
                <table class="table table-sm table-bordered table-hover table-striped small">
                    <thead>
                    <tr>
                        <th>№</th>
                        <th>{{ __('advertiser.registries.index.type') }}</th>
                        <th>{{ __('advertiser.registries.index.date') }}</th>
                        <th></th>
                        <th>{{ __('advertiser.registries.index.total') }}</th>
                        <th>{{ __('advertiser.registries.index.payed') }}</th>
                        <th>{{ __('advertiser.registries.index.moved') }}</th>
                        <th>{{ __('advertiser.registries.index.detail') }}</th>
                        <th>{{ __('advertiser.registries.index.payments') }}</th>
                        <th>{{ __('advertiser.registries.index.status') }}</th>
                        <th>{{ __('advertiser.registries.index.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($collection as $reestr)
                        <tr @if($reestr->status != 1)class=" table-warning" @endif>
                            <td><a href="{{ route("advertiser.reestrs.show", $reestr) }}" title="Заполнение реестра с «единичками», отмена реестра">#{{ $reestr->reestr_id }}</a></td>
                            <td> ЮЛ </td>
                            <td class="text-right text-nowrap">{{ $reestr->datetime->format('Y-m-d') }}</td>
                            <td class="text-right"> </td>
                            <td class="text-right">
                                @money( $reestr->total )
                            </td>
                            <td class="text-right">@money( $reestr->payed)</td>
                            <td class="text-right"> </td>
                            <td class="text-center">
                                <a href="{{ route('advertiser.orders.index', ['reestr_id' => $reestr->reestr_id]) }}">
                                    {{ __('advertiser.registries.index.detail') }}
                                </a>
                            </td>
                            <td  class="text-center">
                                <a href="{{ route("advertiser.reestrs.export", $reestr->reestr_id) }}">
                                    {{ __('advertiser.registries.index.export') }}
                                </a>
                            </td>
                            <td>{{ \App\Lists\PaymentStatusesList::getList()[$reestr->status]  }}</td>
                            <td>
                                <a href="{{ route("advertiser.reestrs.show", $reestr) }}"
                                   title="Заполнение реестра с «единичками», отмена реестра">{{ __('advertiser.registries.index.review') }}</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                {{ $collection->links() }}
            </div>
        </main>
    </x-box>
@endsection
