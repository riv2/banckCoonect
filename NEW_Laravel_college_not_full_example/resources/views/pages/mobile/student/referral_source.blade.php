@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Student panel page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body row">

                    <form action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="col-12">
                            <p>{{ __('How did you find out about Miras University?') }}</p>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral1" type="radio" name="referral" v-model="selectedReferral" value="Official site" />
                                <label class="form-check-label" for="selectedReferral1">
                                    {{ __('Official site') }}
                                </label>
                            </div>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral2" type="radio" name="referral" v-model="selectedReferral" value="Social networks" />
                                <label class="form-check-label" for="selectedReferral2">
                                    {{ __('Social networks') }}
                                </label>
                            </div>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral3" type="radio" name="referral" v-model="selectedReferral" value="Advertising in the city" />
                                <label class="form-check-label" for="selectedReferral3">
                                    {{ __('Advertising in the city') }}
                                </label>
                            </div>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral4" type="radio" name="referral" v-model="selectedReferral" value="Call center" />
                                <label class="form-check-label" for="selectedReferral4">
                                    {{ __('Call center') }}
                                </label>
                            </div>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral5" type="radio" name="referral" v-model="selectedReferral" value="Presentation" />
                                <label class="form-check-label" for="selectedReferral5">
                                    {{ __('Presentation') }}
                                </label>
                            </div>
                            <div class="form-check margin-b15">
                                <input id="selectedReferral6" type="radio" name="referral" v-model="selectedReferral" value="At the invitation of the agitator" />
                                <label class="form-check-label" for="selectedReferral6">
                                    {{ __('At the invitation of the agitator') }}
                                </label>


                                <div class="col-12" v-if="selectedReferral == 'At the invitation of the agitator'" style="margin-top: 20px">

                                    <input type="text" name="referral_agitator" class="form-control" v-on:keyup="searchAgitator()" v-model="selectedAgitator" v-bind:placeholder="'{{ __('Full name') }}'" />
                                    <ul class="dropdown-menu" v-bind:style="{display: agitatorList.length > 0 ? 'block' : 'none'}" style="overflow-y: auto; max-height: 150px;position: relative;">
                                        <li v-for="(agitator, key) in agitatorList">
                                            <a style="cursor: pointer" v-on:click="agitatorSelect(agitator)">@{{ agitator.name + ' (' + agitator.phone + ')' }}</a>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                            <div class="form-check">
                                <input id="selectedReferral7" type="radio" name="referral" v-model="selectedReferral" value="Other" />
                                <label class="form-check-label" for="selectedReferral7">
                                    {{ __('Other') }}
                                </label>
                                <div class="col-12" v-if="selectedReferral == 'Other'" style="margin-top: 20px">
                                    <input type="text"
                                           name="other"
                                           v-model="otherReferral"
                                           class="form-control"
                                           required
                                           v-bind:placeholder="'{{ __('Choose your option') }}'" />
                                </div>
                            </div>
                        </div>

                        <div class="col-12" style="margin-top: 20px">
                            <button type="submit" class="btn btn-info btn-lg">{{ __('Next') }}</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#main-form',
            data: {
                selectedReferral: '',
                otherReferral: '',
                agitatorList: [],
                selectedAgitator: ''
            },
            methods: {
                searchAgitator: function() {
                    if(this.selectedAgitator == '')
                    {
                        this.agitatorList = [];
                        return;
                    }

                    var self = this;
                    axios.post('{{route('agitatorAjaxList')}}', {
                        text: this.selectedAgitator
                    })
                        .then(function(response){
                            self.agitatorList = response.data;
                        });
                },
                agitatorSelect: function(agitator) {
                    this.selectedAgitator = agitator.name + '(' + agitator.phone + ')';
                    this.agitatorList = [];
                }
            },
            watch: {
                agitatorList: function()
                {
                    console.log(this.agitatorList);
                }
            }
        });
    </script>
@endsection