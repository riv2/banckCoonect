@extends('layouts.app')

@section('title', __('Specify card and bank'))

@section('content')

    <section class="content">
        <div class="container-fluid" id="input-bank">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Specify card and bank')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <div v-if="errorMessage" :class="{ 'alert-danger': isError, 'alert-success': !isError }" class="alert margin-t20 margin-b20">
                                <div v-html="errorMessage"> </div>
                            </div>

                            <ul class="list-group margin-b10">

                                <li class="list-group-item">
                                    {{__('Specify the Bank')}}:
                                    <select v-model="bank_id" class="form-control" name="bank_id" >
                                        @if( !empty($banks) && (count($banks) > 0) )
                                          @foreach($banks as $itemBank)
                                                <option value="{{ $itemBank->id }}"> {{ $itemBank->name }} </option>
                                          @endforeach
                                        @endif
                                    </select>
                                </li>
                                <li class="list-group-item">
                                    {{__('Specify the IBAN')}}:
                                    <input v-model="iban" class="form-control" type="text" name="iban" />
                                </li>

                            </ul>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button @click="saveRequest" type="button" class="btn btn-info">
                                        {{__("Continue")}}
                                    </button>
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

        var taskApp = new Vue({
            el: '#input-bank',
            data: {

                bank_id: '',
                iban: '',
                isError: false,
                errorMessage: ''
            },
            methods: {

                saveRequest: function(){

                    this.isError = false;
                    this.errorMessage = '';

                    var bankid = $("select[name=bank_id]");
                    var iban   = $("input[name=iban]");

                    if( bankid.val() == '' ){

                        bankid.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('Bank cannot be empty') }}';
                        return;
                    }

                    if( iban.val().length == 0 ){

                        iban.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('IBAN cannot be empty') }}';
                        return;
                    }

                    if( iban.val().length < 10 ){

                        iban.addClass('error');
                        this.isError = true;
                        this.errorMessage = '{{ __('IBAN cannot be less than 10 characters') }}';
                        return;
                    }

                    var self = this;
                    axios.post('{{ route('agitatorRegisterProfileIbanPost') }}',{
                        "_token": "{{ csrf_token() }}",
                        "bank_id": this.bank_id,
                        "iban": this.iban
                    })
                        .then(function(response){

                            if( response.data.status ) {

                                window.location.href = '{{ route('agitatorRegisterProfileFinish') }}';
                            } else {

                                self.isError = true;
                                self.errorMessage = response.data.status;

                            }
                        })
                        .catch( error => {

                        console.log(error)
                });

                }

            }
        });

    </script>
@endsection

