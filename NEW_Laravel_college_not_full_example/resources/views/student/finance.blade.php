@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="user_panel">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Finances')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div class="accordion padding-t10 accordion-bg" id="accordionFinance">

                                <!-- balance -->
                                <div class="card">
                                    <div class="card-header" id="headingBalance" data-toggle="collapse" data-target="#balance" aria-expanded="true" aria-controls="balance">
                                      <h4 class="mb-0 cursor-pointer">
                                          {{ __("Balance") }}
                                      </h4>
                                    </div>

                                    <div id="balance" class="collapse show row" aria-labelledby="headingBalance" data-parent="#accordionFinance">
                                        <div class="col-12 padding-15">
                                            <p>{{__("Your current balance is")}}: <span> @{{ balanceValue }} </span> ₸</p>
                                            <p> <button :disabled="balanceSendRequest" @click="balanceUpdate" class="btn btn-info" type="button">{{__("Update balance")}}</button> </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- discounts -->
                                <div class="card">
                                    <div class="card-header collapsed" id="headingDiscounts" data-toggle="collapse" data-target="#discounts" aria-expanded="true" aria-controls="balance">
                                      <h4 class="mb-0 cursor-pointer">
                                          {{ __("Discounts") }}
                                      </h4>
                                    </div>

                                    <div id="discounts" class="collapse row" aria-labelledby="headingDiscounts" data-parent="#accordionFinance">

                                        <div class="col-12 padding-15">

                                            <h4>
                                                {{ __('If you applied for a discount, please wait for confirmation from the University and recalculation of the cost of 1 credit. Otherwise, the cost of 1 credit will be displayed without a discount. Refunds on purchased credits are not provided')}}
                                            </h4>

                                            @if( $discountCount == 0 )
                                                <form class="margin-b15" method="POST" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <div class="form-group category">
                                                        <label>{{__('Discount category')}}</label>
                                                        <select class="form-control" name="category" data-size="5" title="{{ __('Please select') }}" required>
                                                            @foreach($categories as $item)
                                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group type">
                                                        <label>{{__('Discount type')}}</label>
                                                        <select class="form-control" name="type_id" data-size="5" title="{{ __('Please select') }}" required>
                                                            @foreach($discountTypes as $item)
                                                                @if(!$item->hidden AND $profile->alien != $item->citizen)
                                                                    <option category="{{$item->category_id}}" value="{{$item->id}}">{{$item['name_' . app()->getLocale() ]}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="form-group filesList">
                                                        <label>{{__('Confirmation document')}}</label>
                                                        <div class="fileInputs">
                                                            <input class="col-12" type="file" name="image1" id="image1" />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div id="addFile" class="btn btn-info"> + {{__('Add file')}}</div>
                                                    </div>
                                                    <div class="form-group padding-15">
                                                        <input type="submit" value="{{__('Submit')}}" class="btn btn-info" />
                                                    </div>

                                                </form>
                                            @endif

                                            @if( count($discountHistory) > 0 )
                                                <div>
                                                    <h5>{{__('Discount request list')}}:</h5>
                                                    <ul class="list-group">
                                                        @foreach($discountHistory as $item)
                                                            <li class="list-group-item">{{$item['name_' . app()->getLocale()]}}
                                                                @if( isset($item->comment) )
                                                                    <br /><b>{{__('Comment')}}:</b> {{$item->comment}}
                                                                @endif
                                                                <br /><b>{{__('Status')}}:</b> {{__($item->status)}}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>

                                <!-- pay -->
                                <div class="card">
                                    <div class="card-header collapsed" id="headingPay" data-toggle="collapse" data-target="#pay" aria-expanded="true" aria-controls="balance">
                                      <h4 class="mb-0 cursor-pointer">
                                          {{ __("Pay") }}
                                      </h4>
                                    </div>

                                    <div id="pay" class="collapse raw" aria-labelledby="headingPay" data-parent="#accordionFinance">
                                        <div class="col-12 padding-15">

                                            <h4> {{__('You can pay for educational services in any convenient way')}}: </h4>

                                            <p> <strong> {{__('Bank cards')}} </strong> <br>
                                                {{__('We accept cards of any banks with open access for operations on the Internet.')}}

                                            </p>

                                            <p>
                                                <a class="btn btn-primary" href="{{ route('studentPayToBalance') }}">{{ __("Pay") }}</a>
                                            </p>

                                            <p> <strong> {{__('University ticket office')}} </strong> <br>
                                                {{__('Shymkent, st. Sapak Datka 2, Miras University building. Opening hours Mon-Fri 9 am - 6 pm')}}
                                            </p>

                                            <p> <strong> {{__('KASPI.KZ')}} </strong> <br>
                                                {{__('Online in the kaspi.kz application without commission. Payments → Education → Payment for universities → Shymkent → Name of the university Miras → University Miras')}}

                                            </p>

                                            <div class="card-header">
                                                {{__('Payments made before 18:00 of the business day will be credited to the account on the same business day. Payments made after working hours, as well as holidays and weekends, will be credited on the next business day.')}}
                                            </div>

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

                                <!-- audit -->
                                <div class="card">
                                    <div class="card-header collapsed" id="headingAudit" data-toggle="collapse" data-target="#audit" aria-expanded="true" aria-controls="balance">
                                      <h4 class="mb-0 cursor-pointer">
                                          {{ __("Audit") }}
                                      </h4>
                                    </div>

                                    <div id="audit" class="collapse raw" aria-labelledby="headingAudit" data-parent="#accordionFinance">
                                        <div class="padding-15">

                                            <div class="alert alert-info alert-dismissible" role="alert">
                                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                {{ __('The request for the transaction to take up to 5 min') }}
                                            </div>

                                            <div v-if="auditMessage" :class="{ 'alert-danger': auditIsError, 'alert-success': !auditIsError }" class="alert">
                                                @{{ auditMessage }}
                                            </div>

                                            <div class="card">
                                                <div class="card-body">

                                                    <h5 class="card-title margin-b10"> {{ __('Transaction search') }} </h5>
                                                    <div class="clearfix"></div>

                                                    <div class="form-group">
                                                        <label>{{__('Date from')}}</label>
                                                        <input v-model="auditDateFrom" id="date_from" class="form-control" type="date" value="" maxlength="9">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{__('Date to')}}</label>
                                                        <input v-model="auditDateTo" id="date_to" class="form-control" type="date" value="" maxlength="9">
                                                    </div>

                                                    <div class="form-group">
                                                        <button @click="auditAddtHistory" :disabled="auditSendRequest" class="btn btn-info" type="button"> {{ __('Search') }} </button>
                                                    </div>

                                                </div>
                                            </div>


                                            <div class="table-responsive no-padding">
                                                <table id="data-table-history" class="table table-striped" style="width:100%;">
                                                    <thead>
                                                    <tr>
                                                        <th> {{ __('Type') }} </th>
                                                        <th> {{ __('Code') }} </th>
                                                        <th> {{ __('Name') }} </th>
                                                        <th> {{ __('Cost') }} </th>
                                                        <th> {{ __('Date') }} </th>
                                                    </tr>
                                                    </thead>

                                                </table>
                                            </div>


                                        </div>
                                    </div>
                                </div>


                                <!-- refund -->
                                <div class="card" v-if=refundAllowed>
                                    <div class="card-header collapsed" id="headingRefunds" data-toggle="collapse" data-target="#refunds" aria-expanded="true" aria-controls="balance">
                                      <h4 class="mb-0 cursor-pointer">
                                          {{ __("Refunds") }}
                                      </h4>
                                    </div>

                                    <div id="refunds" class="collapse raw" aria-labelledby="headingRefunds" data-parent="#accordionFinance">
                                        <div class="padding-15">

                                            <p>{{__('Dear student, you can make a refund in the amount of 10,000 tenge online, indicating the data for the formation of the payment order. Please note that refunds can only be made to the student’s personal card.')}}<br />
                                            {{__('If you want to make a refund of a different amount, please contact the Registrar\'s Office with a request.')}}
                                            </p>

                                            <div v-if="refundMessage" :class="{ 'alert-danger': refundIsError, 'alert-success': !refundIsError }" class="alert">
                                                @{{ refundMessage }}
                                            </div>

                                            <div v-if="refundFileName" class="alert alert-info">
                                                {{__('URL for reference')}}: <a :href="refundFileName" target="_blank">@{{ refundFileName }}</a>
                                            </div>

                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="clearfix"></div>
                                                    <div id="loader-layout" style="position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.5);text-align: center;" v-if="loader"><img src="{{ URL::to('assets/img/load.gif') }}" style="opacity: 0.5; max-width: 100px;"></div>
                                                    <div v-if="(refundReferencePaid && refundSmsSent)">
                                                        
                                                        <div class="form-group input-group">
                                                            <label style="width: 100%;">{{__('Please fill in the code which we texted to you')}}:</label>
                                                            <input v-model="refundSmsField" id="refundSmsField" class="form-control" type="input">
                                                        </div>
                                                        <div class="form-group">
                                                            <button @click="refundSms" class="btn btn-default" type="button"> {{ __('Send') }} </button>
                                                        </div>
                                                    </div>
                                                    <div v-if="(refundReferencePaid && !refundSmsSent)">
                                                        <div class="form-group">
                                                            <label>{{__('INN')}}</label>
                                                            <input id="refundIin" class="form-control" type="input" :value="iin" disabled="disabled">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{__('Bank')}}</label>
                                                            <select class="form-control" v-model="refundBank">
                                                                    <option disabled selected>{{__('Please select')}}</option>
                                                                @foreach($banks as $bank)
                                                                    <option value="{{$bank->id}}">{{$bank->name}} ({{$bank->bic}})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="form-group input-group">
                                                            <label style="width: 100%;">IBAN</label>
                                                            <div class="input-group-prepend">
                                                              <div class="input-group-text">KZ</div>
                                                            </div>
                                                            <input v-model="refundIban" id="refundIban" class="form-control" type="input" v-on:input="refundIbanCheck">
                                                        </div>

                                                        <div class="form-group">
                                                            <label>{{__('Amount')}}</label>
                                                            <input class="form-control" type="input" value="10000 {{__('tenge')}}" disabled="disabled">
                                                        </div>

                                                        <div class="form-group">
                                                            <button @click="refundRequest" class="btn btn-info" type="button"> {{ __('Send request') }} </button>
                                                        </div>
                                                    </div>

                                                    <div v-if="iin">
                                                        <p v-if="(!refundReferencePaid && !refundSmsSent)">
                                                            {{__('To send a request you need to confirm the current status and available balance balance by creating an electronic certificate. Reference cost 500 tenge')}}
                                                            <div class="form-group">
                                                                <button @click="refundReferencePay" v-if="!refundReferencePaid" class="btn btn-info" type="button"> {{ __('Pay') }} </button>
                                                            </div>
                                                        </p>
                                                    </div>
                                                    <div v-if="!iin">
                                                        <p>{{_('You should be a student')}}</p>
                                                    </div>
                                                </div>
                                            </div>

                                            @if(count($refunds) > 0)
                                                <h4>{{__('References list')}}</h4>
                                            @endif
                                            @forEach($refunds as $refund)
                                                @if($refund->status == app\Refund::STATUS_REFERENCE)
                                                    @continue
                                                @endif
                                                <div class="card">
                                                    <div class="card-header">
                                                        <b>{{ __('Refund request from') }}: {{ date("d.m.Y H:i", strtotime('+6 hours', strtotime($refund->created_at))) }}</b>
                                                    </div>
                                                        <div class="card-body">
                                                            <b>{{ __('Status') }}:</b> {{__($refund->status)}}
                                                            <br />
                                                            <small class="text-muted">
                                                                <b>{{ __('Bank') }}:</b> {{$refund->bank}} <b>IBAN:</b> {{ app\Refund::IBAN_KZ . $refund->user_iban}}
                                                            </small>
                                                            {{--<td>{{$refund->tiyn/100}}</td>--}}
                                                            <br />
                                                                <a href={{ $refund->doc }} target="_blank">
                                                                    {{ __('Reference') }}
                                                                </a>
                                                        </div>
                                                </div>
                                            @endForEach


                                        </div>
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
                balanceSendRequest: false,

                auditDateFrom: '',
                auditDateTo: '',
                auditIsError: false,
                auditMessage: false,
                auditSendRequest: false,

                refundAllowed: false,
                refundReferencePaid: {{$refundReferencePaid}},
                refundBank: '{{ $lastBankId }}',
                refundIban: '{{ $lastIban }}',
                refundMessage: null,
                refundIsError :false,
                refundFileName: {!!$filename!!},
                iin: '{{ $profile->iin }}',
                refundSmsSent: {{$refundSmsSent}},
                refundSmsField: null,


                loader: false

            },
            methods: {

                refundIbanCheck: function(){
                    for (i = 0; i < 3; i++) {
                        app.refundIban = app.refundIban.toUpperCase().replace('KZ', '').replace(' ', '');
                    }
                    if(app.refundIban.length > 18) {
                        app.refundIban = app.refundIban.slice(0 , 18);
                    }
                },
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
                },
                refundReferencePay: function(){
                    if (!confirm("{{__('Are you sure you want to buy the reference')}}?")) {
                        return;
                    }
                    app.loader = true;
                    app.refundIsError = true;
                    axios.post('{{ route('refundReferencePay') }}',{
                        "_token": "{{ csrf_token() }}"
                    })
                        .then(function( response ){

                            if ( response.data.status == 'success' ){
                                app.refundIsError = false;
                                app.refundReferencePaid = true;
                                app.refundFileName = response.data.filename;
                            } else {
                                console.log( response.data.message );
                            }
                            app.refundMessage = response.data.message;
                            app.loader = false;
                        });
                },
                refundRequest: function(){
                    app.refundIsError = true;

                    if(app.refundBank === null || app.refundBank === '' || app.refundIban === null) {
                        app.refundMessage = '{{__('Please fill in all fields')}}';
                        return false;
                    }
                    if(app.refundIban === '71722S000001615748' || app.refundIban.length < 18) {
                        app.refundMessage = '{{__('IBAN number is incorrect')}}';
                        app.refundIban = null;
                        return false;
                    }
                    app.loader = true;
                    axios.post('{{ route('refundRequest') }}',{
                        "_token": "{{ csrf_token() }}",
                        'bankId': app.refundBank,
                        'bankIban': app.refundIban
                    })
                        .then(function( response ){

                            if( response.data.status == 'success' ){
                                app.refundIsError = false;
                                app.refundSmsSent = true;
                            } else {
                                console.log( response.data.message );
                            }
                            app.refundMessage = response.data.message;
                            app.loader = false;
                        });
                },
                refundSms: function(){
                    app.refundIsError = true;
                    if(app.refundSmsField === null) {
                        app.refundMessage = '{{__('Please fill in SMS code')}}'
                        return false;
                    }
                    app.loader = true;
                    axios.post('{{ route('refundSmsCode') }}',{
                        "_token": "{{ csrf_token() }}",
                        'sms_code': app.refundSmsField
                    })
                        .then(function( response ){

                            if( response.data.status == 'success' ){
                                app.refundIsError = false;
                                app.refundReferencePaid = false;
                                app.refundSmsSent = false;
                            } else {
                                app.refundSmsSent = false;
                                console.log( response.data.message );
                            }
                            app.refundMessage = response.data.message;
                            app.loader = false;
                        });
                },
                auditAddtHistory: function(){

                    this.auditSendRequest = true;
                    this.auditIsError = false;
                    this.auditMessage = false;

                    if( !this.auditDateFrom || ( this.auditDateFrom == '' ) ){

                        this.auditSendRequest = false;
                        this.auditIsError = true;
                        this.auditMessage = '{{ __('Error, date from required') }}';
                        return;
                    }
                    if( !this.auditDateTo || ( this.auditDateTo == '' ) ) {

                        this.auditSendRequest = false;
                        this.auditIsError = true;
                        this.auditMessage = '{{ __('Error, date to required') }}';
                        return;
                    }

                    var self = this;
                    axios.post('{{ route('profileAjaxAddTransactionHistory') }}',{
                        "_token": "{{ csrf_token() }}",
                        "iin": app.iin,
                        "date_from": self.auditDateFrom,
                        "date_to": self.auditDateTo
                    })
                        .then(function(response){

                            if( response.data.status ){

                                self.auditMessage = response.data.message;

                            } else {

                                self.auditIsError = true;
                                self.auditMessage = '{{ __('Request error') }}';
                            }

                        });

                    this.auditSendRequest = false;

                }
            },
            created: function(){

                this.balanceValue = '{{ $balance }}';

                if( {{$profile->alien}} == 1 ) {
                    this.refundAllowed = false;
                }
            }
        });

        window.onload = function(){
            $('.type .selectpicker').find('option').hide();
            $('.category .selectpicker').on('change', function(){
                var categoryID = this.value;
                var typesCount = 0;
                $('.type select').find('option').each(function(){
                    var typeCategory = $(this).attr('category');
                    if(typeCategory != categoryID) {
                        $(this).hide();
                    } else {
                        $(this).show();
                        typesCount++;
                    }

                });
                if(typesCount == 1 ) {
                    var val = $(".type select").find('option[category="'+categoryID+'"]').val();
                }

                $(".type .selectpicker").val(val).selectpicker("refresh");
            });


            var filesAdded = 1;
            $('#addFile').click(function() {
                filesAdded++;
                $('.filesList .fileInputs').append(
                    $('<input/>')
                        .attr('type', 'file')
                        .attr('name', 'image'+filesAdded)
                        .attr('id', 'image'+filesAdded)
                        .attr('class', 'col-12')
                );
            });

            var dataTableHistory = $('#data-table-history').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
                },
                "processing": true,
                "serverSide": true,
                "ajax":{
                    url :"{{ route('profileAjaxGetTransactionHistory') }}",
                    data:{
                        "_token": "{{ csrf_token() }}"
                    },
                    type: "post",
                    error: function(){
                        $(".employee-grid-error").html("");
                        $("#employee-grid").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                        $("#employee-grid_processing").css("display","none");
                    }
                }
            });

        };


        function updateBalance() {
            $('#balance-button').attr('disabled', true);
            $.ajax({
                url:'{{ route('studentBalanceUpdate') }}',
                type:'post',
                data: {},
                success: function(response){
                    $('#balance-label').html(response.balance);
                    $('#balance-button').attr('disabled', false);
                },
                error: function(){
                    $('#balance-button').attr('disabled', false);
                }
            });
        }

    </script>
@endsection

