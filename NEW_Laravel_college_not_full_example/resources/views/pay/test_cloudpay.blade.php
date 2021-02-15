@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('For payment')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">



                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div class="alert alert-success" v-cloak v-show="resultSuccess">{{ __('Payment completed successfully') }}</div>
                            <div class="alert alert-error" v-cloak v-show="errorMessage">@{{errorMessage}}</div>
                            <form class="form-horizontal">
                                <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                                <div class="input-group col-12">
                                    <input type="number" v-model="amount" class="form-control" name="amount" required />
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="basic-addon2">KZT</span>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xs-12" style="padding-top: 20px;">
                                    <a class="btn btn-primary" v-on:click="pay()">{{__('Replenish account')}}</a>
                                </div>

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
    <script src="https://widget.cloudpayments.ru/bundles/cloudpayments"></script>

    <script type="text/javascript">
        var app = new Vue({
            el: '#app',
            data: {
                amount: 0,
                errorMessage: '',
                resultSuccess: false,
            },
            methods: {
                pay: function () {
                    this.resultSuccess = false;
                    this.errorMessage = '';

                    var self = this;
                    var widget = new cp.CloudPayments();
                    widget.charge({ // options
                            publicId: 'pk_f5d4e8e1527a6bb5a1f9d57e437d4',  //id из личного кабинета
                            description: 'Пример оплаты (деньги сниматься не будут)', //назначение
                            amount: parseFloat(this.amount), //сумма
                            currency: 'KZT', //валюта
                            invoiceId: '1234567', //номер заказа  (необязательно)
                            accountId: 'user@example.com', //идентификатор плательщика (необязательно)
                            skin: "mini" //дизайн виджета
                        },
                        function (options) { // success
                            self.resultSuccess = true;
                        },
                        function (reason, options) { // fail
                            self.errorMessage = reason;
                        });
                }
            }
        });
    </script>
@endsection