@extends('layouts.app')

@section('title', __('Installment plan'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="partial-buy-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Discipline Pay page')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <form @submit="checkForm" action="{{route('studentPay', ['id' => $studentDiscipline->discipline->id])}}" method="get">
                                <input type="hidden" name="submodule_id" value="{{$submoduleId}}" />

                                <p>@lang('You going to pay for discipline') <strong>"{{$studentDiscipline->discipline->name}}"</strong>.</p>
                                <p>@lang("Count"): <input type="button" class="btn btn-default" v-on:click="creditsDown();" value="-"> <input type="text" class="text-center" readonly value="1" v-model="credits" style="width:50px;" name="credits"> <input type="button" class="btn btn-default" v-on:click="creditsUp();" value="+"> @lang("credit")</p>
                                <p class="bg-danger" v-if="error" v-cloak class="hidden">@lang('Not enough funds on balance')</p>
                                <p>@lang("Credit price"): {{$creditPrice}} тг</p>
                                <p>@lang("Pay sum"): <span v-html="sum"></span> тг</p>

                                <input
                                        type="submit"
                                        class="btn btn-info"
                                        value="@lang('To pay')"
                                        id="btnSubmit"
                                        :disabled="processing"
                                > <a href="{{route('studentPayToBalance')}}" class="btn btn-success" v-if="error" v-cloak>@lang('Replenish balance')</a>

                                <a href="{{route('study')}}" class="btn btn-default" :class="{disabled: processing}">@lang('Cancel')</a>
                            </form>
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
        const MAX_CREDITS = {{$maxCredits}};
        const CREDIT_PRICE = {{$creditPrice}};
        const USER_BALANCE = {{$userBalance}};

        var app = new Vue({
            el: "#partial-buy-app",
            data: {
                credits: 0,
                sum : 0,
                error: false,
                processing: false
            },
            methods: {
                creditsUp: function() {
                    if (this.credits < MAX_CREDITS) {
                        this.credits++;
                    }
                },
                creditsDown: function() {
                    if (this.credits > 1) {
                        this.credits--;
                    }
                },
                setSum: function() {
                    this.sum = this.credits * CREDIT_PRICE;
                },
                checkForm: function (e) {
                    this.processing = true;

                    if (confirm('@lang('Do you confirm this purchase?')')) {
                        return true;
                    }

                    this.processing = false;

                    e.preventDefault();
                }
            },
            watch: {
                credits: function(newVal, oldVal) {
                    this.sum = newVal * CREDIT_PRICE;

                    console.log(this.sum);
                    console.log(USER_BALANCE);
                    this.error = this.sum > USER_BALANCE;

                },
                error: function(newVal, oldVal) {
                    $('#btnSubmit').attr('disabled', newVal);
                }
            },
            created: function() {
                this.credits = 1;
            }
        });
    </script>
@endsection

