@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="add-agitator">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('The choice of the agitator')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <br>
                            <div v-if="agitatorMessage" :class="{ 'alert-danger': agitatorIsError, 'alert-success': !agitatorIsError }" class="alert">
                                <div v-html="agitatorMessage"> </div>
                            </div>


                            <table class="table margin-b30">
                                <tr>
                                    <td>
                                        <button @click="agitatorAddAgitatorShow = true" class="btn btn-success" type="button"> @lang('YES / I want to point out the agitator who took part in my choice of University') </button>
                                    </td>
                                    <td>
                                        <a class="btn btn-primary" href="{{ route('profileWithoutAgitator') }}"> @lang('NO / I made the decision to go to University') </a>
                                    </td>
                                </tr>
                            </table>


                            {{-- AGITATOR INPUT --}}
                            <div v-if="agitatorAddAgitatorShow" class="margin-b30">

                                <div class="alert alert-info" role="alert">
                                    @lang('We inform you that this person will receive a reward of 10,000 to the account, since you specified it in the Agitator field. If you have friends to whom you could recommend our University, let them specify your phone number in the line Agitator when making a package of documents for admission, and You will be able to get your reward after passing a short registration procedure for the agitator.')
                                </div>

                                <div class="form-group">
                                    <label for="education_lang" class="col-md-4 control-label">{{__('Specify the name of the agitator')}}</label>
                                    <div class="col-md-6">
                                        <input v-model="agitatorFio" @change="agitatorChangeInputFio" class="form-control" type="text" name="fio" value="" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="education_lang" class="col-md-4 control-label">{{__('Or phone number')}}</label>
                                    <div class="col-md-6">
                                        <input v-model="agitatorPhone" @change="agitatorChangeInputPhone" class="form-control" type="text" name="phone" value="" />
                                    </div>
                                </div>

                            </div>


                            {{-- AGITATOR SHOW --}}
                            <div v-if="agitatorAgitatorDataShow" class="margin-b30">
                                <table class="table">
                                    <tr>
                                        <td>
                                            <p><strong>@lang('Your agitator'):</strong> @{{ agitatorData.fio }}</p>
                                        </td>
                                        <td>
                                            <img class="rounded"
                                                 :src="agitatorData.avatar"
                                                 :alt="agitatorData.fio"
                                                 style="max-width: 100%; border: 1px solid #ddd"
                                            />
                                        </td>
                                    </tr>
                                </table>

                                <br>
                                <button @click="agitatorAddAgitator" class="btn btn-success" type="button"> @lang('Select agitator') </button>

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

            el  : "#add-agitator",
            data: {
                agitatorIsError: false,
                agitatorMessage: '',
                agitatorFio: '',
                agitatorPhone: '',
                agitatorAddAgitatorShow: false,
                agitatorAgitatorDataShow: false,
                agitatorRequest: false,
                agitatorData: false
            },
            methods: {

                agitatorLoad: function(fio,phone){

                    this.agitatorRequest = true;
                    this.agitatorIsError = false;
                    this.agitatorMessage = '';

                    var self = this;
                    axios.post('{{ route("profileAjaxGetAgitator") }}',{
                        "_token": "{{ csrf_token() }}",
                        "phone": phone,
                        "fio": fio
                    })
                    .then(function(response){

                        if( response.data.status ){

                            self.agitatorData = response.data.data;
                            self.agitatorAgitatorDataShow = true;
                        } else {

                            self.agitatorAgitatorDataShow = false;
                            self.agitatorIsError = true;
                            self.agitatorMessage = response.data.message;
                        }

                    })
                    .catch( error => {

                        console.log( response );
                    })
                    .finally( () => ( self.agitatorRequest = false ));


                },
                agitatorAddAgitator: function(){

                    this.agitatorRequest = true;
                    this.agitatorIsError = false;
                    this.agitatorMessage = '';

                    var self = this;
                    axios.post('{{ route("profileAddAgitatorPost") }}',{
                        "_token": "{{ csrf_token() }}",
                        "fio": this.agitatorData.fio,
                        "phone": this.agitatorData.phone
                    })
                    .then(function(response){

                        if( response.data.status ){

                            window.location.href = '{{ route('profileRegisterPayment') }}';
                        } else {

                            self.agitatorIsError = true;
                            self.agitatorMessage = response.data.message;
                        }

                    })
                    .catch( error => {
                        console.log( response );
                    })
                    .finally( () => ( self.agitatorRequest = false ));


                },
                agitatorChangeInputFio: function(){

                    this.agitatorPhone = '';
                    this.agitatorLoad(this.agitatorFio,null);
                },
                agitatorChangeInputPhone: function(){

                    this.agitatorFio = '';
                    this.agitatorLoad(null,this.agitatorPhone);
                }
            },
            created: function(){

            }

        });

    </script>
@endsection
