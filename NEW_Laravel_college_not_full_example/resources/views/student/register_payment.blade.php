@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="register-payment">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Payment for paperwork')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <br>
                            <div v-if="paymentMessage" :class="{ 'alert-danger': paymentIsError, 'alert-success': !paymentIsError }" class="alert">
                                <div v-html="paymentMessage"> </div>
                            </div>

                            <!-- COST -->
                            <div class="form-group">
                                <label for="cost" class="col-md-3 control-label">{{__('Cost')}}</label>
                                <div class="col-md-10">
                                    <input class="form-control" type="text" value="{{ $cost ?? 0 }}" readonly />
                                </div>
                            </div>

                            <!-- BALANCE -->
                            <div class="form-group">
                                <label for="cost" class="col-md-3 control-label">{{__('Your current balance is')}}</label>
                                <div class="col-md-10">
                                    <input class="form-control" type="text" value="{{ $balance ?? 0 }}" readonly />
                                </div>
                            </div>

                            <!-- PAY METHOD -->
                            <div class="form-group">
                                <label for="paymethod" class="col-md-3 control-label">{{__('Payment method')}}</label>
                                <div class="col-md-10">
                                    <template v-if="paymentMethod">
                                        <input v-model="paymentPaymentMethod( paymentMethod )" class="form-control" type="text" readonly />
                                    </template>
                                </div>
                            </div>

                            <!-- Button PAY -->
                            <div class="form-group">
                                <div class="col-md-10">
                                    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#paymentModal"> {{ __('Choose payment method') }} </button> &nbsp;
                                    <button class="btn btn-info" type="button" data-toggle="modal" data-target="#paymentInfo"> {{ __('Ways to top up your balance') }} </button>
                                </div>
                            </div>

                            <!-- modal payment -->
                            <div id="paymentModal" class="modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> {{ __('Choose payment method') }} </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">


                                            <div class="form-group row">
                                                <label for="paymethod" class="col-12 control-label">{{__('Payment method')}}</label>
                                                <div class="col-12">

                                                    <select v-model="paymentMethod" class="form-control">
                                                        <option>...</option>
                                                        <option value="{{ \App\CourseStudent::PAYMENT_METHOD_BALANCE }}" > {{ __('From balance') }} </option>
                                                    </select>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-info" data-dismiss="modal"> {{ __('Close') }} </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- modal payment info -->
                            <div id="paymentInfo" class="modal" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title"> {{ __('Pay') }} </h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <h4> {{__('You can pay for educational services in any convenient way')}}: </h4>

                                            <p> <strong> {{__('Bank cards')}} </strong> <br>
                                                {{__('We accept cards of any banks with open access for operations on the Internet.')}}

                                            </p>

                                            @if(\App\Services\Auth::user()->id == 10556)
                                                <p>
                                                    <a class="btn btn-primary" href="{{ route('studentPayToBalance') }}">Epay</a>
                                                </p>
                                            @endif

                                            <p> <strong> {{__('University ticket office')}} </strong> <br>
                                                {{__('Shymkent, st. Sapak Datka 2, Miras University building. Opening hours Mon-Fri 9 am - 6 pm')}}
                                            </p>

                                            <p> <strong> {{__('KASPI.KZ')}} </strong> <br>
                                                {{__('Online in the kaspi.kz application without commission. Payments → Education → Payment for universities → Shymkent → Name of the university Miras → University Miras')}}

                                            </p>

                                            <p> <strong> {{__('Payment terminals')}} </strong> <br>
                                            {{__('Addresses of payment terminals of VTB Bank:')}}
                                            <ul>
                                                <li> {{__('St. Ilyaev 3 (Miras University Building)')}}  </li>
                                                <li> {{__('St. Sapak Datka 2 (Miras University Building)')}}  </li>
                                                <li> {{__('Kunayev St. 31 (branch of VTB Bank)')}}  </li>
                                                <li> {{__('St. Turkestan 65 (branch of VTB Bank)')}} </li>
                                            </ul>
                                            {{__('Commission for one payment - 40 tenge')}}
                                            </p>

                                            <p> &nbsp; </p>
                                            <p> <strong>{{__('Payment by legal entities')}}</strong> <br> {{__('If the customer organization pays for you, please download the training contract in the Profile and fill in the details of the organization.')}}  </p>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-info" data-dismiss="modal"> {{ __('Close') }} </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button @click="paymentPaymentProcess" :disabled="paymentPaymentRequest" @if( empty($balance) || ($balance < $cost) ) disabled="disabled"  @endif class="btn btn-info" type="button"> {{ __('To pay') }} </button>


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

        var app = new Vue({

            el  : "#register-payment",
            data: {

                paymentPaymentRequest: false,
                paymentIsError: false,
                paymentMessage: '',
                paymentMethod: false,
                paymentPaymethodList: {
                    "{{ \App\CourseStudent::PAYMENT_METHOD_BALANCE }}": "{{ __('From balance') }}"
                }

            },
            methods: {

                paymentPaymentMethod: function(value){
                    return this.paymentPaymethodList[ value ];
                },
                paymentPaymentProcess: function(){

                    this.paymentPaymentRequest = true;
                    this.paymentIsError = false;
                    this.paymentMessage = '';

                    if(!this.paymentMethod){

                        this.paymentPaymentRequest = false;
                        this.paymentIsError = true;
                        this.paymentMessage = '{{ __('Error, please choose a payment method') }}';
                        return;
                    }

                    var self = this;
                    axios.post('{{ route("profileRegisterPaymentPost") }}',{
                        "_token": "{{ csrf_token() }}",
                        "paymethod": this.paymentMethod
                    })
                    .then(function(response){

                        if( response.data.status ){

                            window.location.href = '{{ route('profileRegisterFinish') }}';
                        } else {

                            self.paymentIsError = true;
                            self.paymentMessage = response.data.message;
                        }

                    })
                    .catch( error => {
                        console.log( response );
                    })
                    .finally( () => ( self.paymentPaymentRequest = false ));

                }

            },
            created: function(){

            }


        });

    </script>
@endsection
