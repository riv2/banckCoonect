<?php
    $user = \App\Services\Auth::user();
?>

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

                            <div class="alert alert-success" v-cloak v-show="resultSuccess">{{ __('Payment completed successfully') }} <a href="{{ route('studentPayToBalance') }}">{{ __('Repeat pay') }}</a></div>
                            <div class="alert alert-error" v-cloak v-show="errorMessage">@{{errorMessage}}</div>

                            <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />

                            <div class="input-group col-12" v-show="!resultSuccess">
                                <input type="number" v-model="amount" class="form-control" minlength="3" name="amount" required />

                                <div class="input-group-append">
                                    <span class="input-group-text" id="basic-addon2"> {{ __('tg') }}</span>
                                </div>
                                <div class="col-md-12">
                                    <sub>{{ __('Minimal pay 1000 kzt') }}</sub>
                                </div>
                            </div>

                            <div class="col-12" style="padding-top: 20px; font-weight: bold;" v-show="!resultSuccess && hasMinimalPay">
                                <p>{{__('Commission for one transaction is')}} 3%</p>
                                <p>{{ __('Charge amount') }} <span style="font-weight: bold;" v-html="amountOtherBank"></span> {{ __('tg') }}</p>
                            </div>
                            <div class="col-12" v-show="!resultSuccess && hasMinimalPay">
                                <input type="checkbox" v-model="saveCard" name="saveCard" v-bind:disabled="!amount" />
                                <span>Сохранить карту для быстрой оплаты</span>
                            </div>

                            <div class="col-md-12 col-xs-12" style="padding-top: 20px;" v-show="cardListCount == 0 && !resultSuccess && hasMinimalPay" v-cloak>
                                <a class="btn btn-primary" v-on:click="payByWidget()" v-bind:class="{disabled: !amount}">{{__('Replenish account')}}</a>
                            </div>

                            <div class="col-md-12" v-show="cardListCount && !resultSuccess && hasMinimalPay" v-cloak>
                                <hr>
                                @foreach($user->payCards as $payCard)
                                <div class="alert col-md-12 no-padding" id="card-block-{{$payCard->id}}">

                                    **** **** **** {{$payCard->last_digits}} ({{$payCard->exp_date}})

                                    <a class="btn btn-primary" v-bind:class="{disabled: !amount}" v-on:click="payByToken({{$payCard->id}})">{{ __('Pay') }}</a>
                                    <a class="btn btn-default" v-on:click="removeCard({{$payCard->id}})">{{ __('Remove card') }}</a>

                                </div>
                                <hr>
                                @endforeach
                                <a class="btn btn-primary" v-on:click="payByWidget()" v-bind:class="{disabled: !amount}"> {{ __('Other card') }}</a>
                            </div>

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
                amount: 1000,
                otherBankPercent: 3,
                errorMessage: '',
                resultSuccess: false,
                payMethod: @if($user->paycloud_token) 'token' @else 'widget' @endif,
                cardListCount: {{ count($user->payCards) }},
                saveCard: false
            },
            computed: {
                amountOtherBank: function(){
                    return Math.ceil(this.amount / 0.97);
                },
                hasMinimalPay: function(){
                    return this.amount >= 1000;
                }
            },
            methods: {
                payByWidget: function() {
                    if(!this.amount) {
                        return false;
                    }

                    if(this.amount < 1000) {
                        alert('{{ __('Minimal pay 1000 kzt') }}');
                        return false;
                    }

                    this.resultSuccess = false;
                    this.errorMessage = '';

                            @if(app()->isLocale('kz'))
                    var locale = 'kk-KZ';
                            @elseif(app()->isLocale('en'))
                    var locale = 'en-US';
                            @else
                    var locale = 'ru-RU';
                            @endif

                    var self = this;
                    var widget = new cp.CloudPayments({language: locale});
                    widget.charge({
                            publicId: '{{ env('CLOUDPAYMENTS_PUBLIC_ID') }}',
                            description: '{{ __('Deposit balance in miras.app') }}',
                            amount: parseFloat(this.amountOtherBank),
                            currency: '{{ env('CLOUDPAYMENTS_CURRENCY') }}',
                            accountId: '{{ \App\Services\Auth::user()->id }}',
                            skin: '{{ env('CLOUDPAYMENTS_SKIN') }}',
                            invoiceId: '{{ \App\Services\Auth::user()->studentProfile->iin . rand(1000, 9999) }}',
                            data: {
                                userId: {{ \App\Services\Auth::user()->id }},
                                saveCard: this.saveCard
                            }
                        },
                        function (options) { // success
                            self.resultSuccess = true;
                            self.payMethod = 'token';
                        },
                        function (reason, options) { // fail
                            if(reason != 'User has cancelled') {
                                self.errorMessage = reason;
                            }
                        });
                },
                payByToken: function(cardId) {
                    if(!this.amount) {
                        return false;
                    }

                    if(!confirm('{{ __('Top up balance?') }}')) {
                        return false;
                    }

                    var self = this;

                    axios.post('{{route('studentPayToBalanceByToken')}}', {
                        amount: this.amountOtherBank,
                        card_id: cardId,
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function(response){
                            if(response.data.status == 'success'){
                                self.resultSuccess = true;
                            } else {
                                self.errorMessage = response.data.message;
                            }
                        });
                },
                removeCard: function(cardId) {
                    if(!confirm('{{ __('Remove card') }}?')) {
                        return false;
                    }

                    var self = this;

                    axios.post('{{route('studentRemoveCard')}}', {
                        card_id: cardId,
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function(response){
                            if(response.data.status == 'success'){
                                $('#card-block-' + cardId).remove();
                            } else {
                                self.errorMessage = response.data.message;
                            }
                        });
                }
            }
        });
    </script>
@endsection