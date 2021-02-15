@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="user_panel">

            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">
                    {{__('Cabinet')}}
                </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#balance" data-toggle="tab"> {{ __("Balance") }} </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#pay" data-toggle="tab"> {{ __("Pay") }} </a>
                                </li>
                            </ul>


                            <div class="tab-content padding-t10">

                                <!-- balance -->
                                <div class="tab-pane row active" id="balance">
                                    <div class="col-12 padding-15">
                                        <p>{{__("Your current balance is")}}: <span> @{{ balanceValue }} </span> ₸</p>
                                        <p> <button :disabled="balanceSendRequest" @click="balanceUpdate" class="btn btn-info" type="button">{{__("Update balance")}}</button> </p>
                                    </div>
                                </div>

                                <!-- pay -->
                                <div class="tab-pane row" id="pay">
                                    <div class="col-12 padding-15">

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
                                </div>

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
    <script type="text/javascript">

        var app = new Vue({
            el: '#user_panel',
            data: {

                balanceValue: 0,
                balanceSendRequest: false

            },
            methods: {

                balanceUpdate: function(){

                    this.balanceSendRequest = true;
                    var self = this;
                    axios.post('{{ route('profileAjaxGetUserBalance') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function( response ){

                            if( response.data.status ){

                                self.balanceValue = response.data.balance;
                            } else {

                                console.log( response.data.message );
                            }
                            self.balanceSendRequest = false;
                        });
                }
            },
            created: function(){

                this.balanceValue = '{{ $balance }}';
            }
        });


    </script>
@endsection