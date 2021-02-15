@extends('layouts.app')

@section('title', __('Discipline Pay page'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="discipline-pay-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Discipline Pay page')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <p>@lang('You going to pay for discipline') <strong>"{{$SD->discipline->name}}"</strong>.</p>
                            <p>@lang("Count"): {{$credits}} @lang("credit")</p>
                            <p>@lang("Credit price"): {{$creditPrice}} тг</p>
                            <p>@lang("Pay sum"):  {{$credits * $creditPrice}} тг</p>

                            <input
                                type="button"
                                class="btn btn-info"
                                value="@lang('To pay')"
                                id="btnSubmit"
                                v-on:click="payClick"
                                :disabled="processing || errors"
                            >

                            <a href="{{route('study')}}" class="btn btn-default" :class="{disabled: processing}">@lang('Cancel')</a>

                            @if ($errors->has('balance'))
                                <a href="{{route('studentPayToBalance')}}" class="btn btn-success">@lang('Replenish balance')</a>
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
        const HAS_ERRORS = @if(count($errors)) true @else false @endif;
        const PAY_ROUTE = '{{route('studentPay', ['id' => $SD->discipline->id, 'credits' => $credits, 'submodule_id' => $submoduleId])}}';

        var app = new Vue({
            el: "#discipline-pay-app",
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