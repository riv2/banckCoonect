@extends('layouts.app')

@section('title', __('Remote Access Payment'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="remote-access-pay-app">
            @if ($lowBalance)
                <div class="alert alert-warning" role="alert">@lang('Not enough funds on balance')</div>
            @endif

            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Remote Access Payment')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <p>@lang('You pay for remote access to the discipline') <strong>"{{$studentDiscipline->discipline->name}}"</strong>.</p>
                            <div>@lang('remote_cost', ['cost' => $priceFor1Credit])</div>
                            <div>@lang('remote_amount', ['amount' => $service->cost])</div>

                            <br>
                            <input
                                type="button"
                                class="btn btn-info"
                                value="@lang('To pay')"
                                id="btnSubmit"
                                v-on:click="payClick"
                                :disabled="processing || errors"
                            >

                            <a href="{{route('study')}}" class="btn btn-default" :class="{disabled: processing}">@lang('Cancel')</a>

                            @if($lowBalance)
                                <a href="{{route('studentPayToBalance', ['id' => $studentDiscipline->discipline->id])}}" class="btn btn-primary" >@lang('Replenish account')</a>
                            @endif
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        const HAS_ERRORS = @if($lowBalance) true @else false @endif;
        const PAY_ROUTE = '{{route('studentPayRemoteAccess', ['id' => $studentDiscipline->discipline->id])}}';

        var app = new Vue({
            el: "#remote-access-pay-app",
            data: {
                errors: false,
                processing: false
            },
            methods: {
                payClick: function () {
                    this.processing = true;

                    if (confirm('@lang('Do you confirm this purchase?')')) {
                        window.location.href = PAY_ROUTE;
                    } else {
                        this.processing = false;
                    }
                }
            }
        });
    </script>
@endsection