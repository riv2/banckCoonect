@extends('layouts.app')

@section('content')
    <div class="login-box" style="background: none;">
        <div class="login-box-body" style="background: none;">
            <div class="col-md-12" style="padding-top: 8vh">
                <div class="col-md-12 text-center" style="padding-bottom: 10px">
                    <p style="font-weight: bold; font-size: 1.2em;">{{ __('Welcome to miras.app') }}</p>
                    <p>{{ __('Study, work and leisure from MIRAS EDUCATION') }}</p>
                    <p style="font-weight: bold">{{ __('Do right!') }}</p>
                </div>
                <form>
                    <div class="input-group form-group flex-nowrap col-md-12 no-padding" v-bind:class="{'d-none':phoneSended}">
                        <input
                                type="text"
                                class="form-control"
                                v-bind:class="{'is-invalid':errorPhone}"
                                placeholder="{{ __('Phone number') }}"
                                v-model="phone"
                                v-on:keyup.enter="getUser()"
                                required />
                    </div>

                    <div class="input-group form-group flex-nowrap col-md-12" v-cloak v-if="smsSended">
                        <input
                                type="text"
                                v-model="smsCode"
                                class="form-control"
                                v-bind:class="{'is-invalid':errorSmsCode}"
                                placeholder="{{ __('SMS code') }}"
                                required />
                    </div>

                    <div class="input-group form-group flex-nowrap col-md-12" v-bind:class="{'d-flex':(phoneSended && !smsSended && (!user || user.mobileConfirm))}" style="display: none;">
                        <input
                                type="password"
                                v-model="password"
                                class="form-control"
                                v-bind:class="{'is-invalid':errorPassword}"
                                v-bind:placeholder="recoveryPassword ? '{{ __('SMS code') }}' : '{{ __('Password') }}'"
                                v-on:keyup.enter="login()"
                                minlength="6"
                                required />
                    </div>

                    <div class="input-group form-group flex-nowrap col-md-12" v-cloak v-if="recoveryPassword && user">
                        <input
                                type="password"
                                v-model="newPassword"
                                class="form-control"
                                v-bind:class="{'is-invalid':errorNewPassword}"
                                placeholder="{{ __('New password') }}"
                                minlength="6"
                                required />
                    </div>

                    <div class="input-group form-group flex-nowrap col-md-12" v-cloak v-if="recoveryPassword || (phoneSended && !user && !smsSended)">
                        <input
                                type="password"
                                v-model="passwordConfirm"
                                class="form-control"
                                v-bind:class="{'is-invalid':errorConfirmPassword}"
                                placeholder="{{ __('Confirm password') }}"
                                minlength="6"
                                required />
                    </div>

                    <div class="col-md-12 no-padding">
                        <button type="button"
                                v-bind:disabled="process"
                                class="btn btn-info btn-block btn-flat text-first-upper"
                                v-if="!phoneSended"
                                v-on:click="getUser()">{{ __('Next') }}</button>
                    </div>

                    <div class="col-md-12 no-padding" v-cloak v-if="phoneSended">
                        <button type="button"
                                v-bind:disabled="process"
                                class="btn btn-info btn-block btn-flat text-first-upper"
                                v-on:click="login()"
                                >{{ __('Enter') }}</button>
                    </div>
                    <div v-if="phoneSended && !smsSended && user && user.mobileConfirm" v-cloak class="col-md-12 no-padding" style="margin-top: 10px">
                        <button
                                type="button"
                                v-bind:disabled="process"
                                v-if="!showSendPasswordType && !infoMessage"
                                class="btn btn-default btn-block btn-flat text-first-upper"
                                v-on:click="sendPassword()">{{ __('Forgot your password?') }}</button>

                        <button
                                type="button"
                                v-bind:disabled="process"
                                v-if="showSendPasswordType"
                                class="btn btn-default btn-block btn-flat text-first-upper"
                                v-on:click="sendPassword('phone')">{{ __('Send as SMS message') }}</button>

                        <button
                                type="button"
                                v-bind:disabled="process"
                                v-if="showSendPasswordType"
                                class="btn btn-default btn-block btn-flat text-first-upper"
                                v-on:click="sendPassword('email')">{{ __('Send as e-mail') }}</button>
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="alert alert-danger col-md-12" role="alert" v-cloak v-show="errorMessage">@{{ errorMessage }}</div>
                    <div class="alert alert-info col-md-12" role="alert" v-cloak v-show="infoMessage">@{{ infoMessage }}</div>
            </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        var app = new Vue({
            el: '#app',
            data: {
                phone: '',
                smsCode: '',
                password: '',
                passwordConfirm: '',
                newPassword: '',
                user: null,
                phoneSended: false,
                process: false,
                smsSended: false,
                recoveryPassword: false,
                errorPhone: false,
                errorPassword: false,
                errorNewPassword: false,
                errorConfirmPassword: false,
                errorSmsCode: false,
                errorMessage: '',
                infoMessage: '',
                sendPasswordType: '',
                showSendPasswordType: false
            },
            methods: {
                getUser: function () {
                    this.errorPhone = false;

                    var login = /(\+)?\d{10,13}/.test(this.phone);

                    if (!login || (this.phone.length < 10) || (this.phone.length > 13)) {
                        this.errorPhone = true;
                        return;
                    }

                    var self = this;
                    this.process = true;

                    axios.post('{{route('loginGetStudent')}}', {
                        login: this.phone
                    })
                        .then(function (response) {
                            self.process = false;

                            if (response.data.message) {
                                self.errorMessage = response.data.message;
                                return;
                            }

                            self.user = response.data.user;
                            self.phoneSended = true;

                            if (self.user && !self.user.mobileConfirm) {
                                self.smsSended = true;
                            }
                        });
                },
                sendSmsCode: function(){
                    var self = this;
                    this.process = true;

                    axios.post('{{route('StudentLoginSendSmsCode')}}', {
                        phone: this.phone
                    })
                        .then(function(response){
                            self.smsSended = true;
                            self.process = false;
                        });
                },
                sendPassword: function(type = ''){
                    this.errorMessage = '';
                    this.password = '';
                    this.recoveryPassword = true;

                    if(!type) {
                        if (!this.user.email) {
                            this.sendPasswordType = 'phone';
                        }

                        if (!this.sendPasswordType) {
                            this.showSendPasswordType = true;
                            return;
                        }
                    } else {
                        this.sendPasswordType = type;
                    }

                    var self = this;

                    axios.post('{{route('StudentLoginSendPassword')}}', {
                        phone: this.phone,
                        type: this.sendPasswordType
                    })
                        .then(function(response){
                            if(response.data.message)
                            {
                                self.errorMessage = response.data.message;
                                self.infoMessage = '';

                            }
                        });

                    self.showSendPasswordType = false;

                    if(self.sendPasswordType == 'phone') {
                        self.infoMessage = '{{ __('Text message has been send to your mobile') }}';
                    }

                    if(self.sendPasswordType == 'email') {
                        self.infoMessage = '{{ __('Message has been send to your e-mail') }}';
                    }
                },
                login: function(){

                    this.errorPhone = false;
                    this.errorPassword = false;
                    this.errorNewPassword = false;
                    this.errorConfirmPassword = false;
                    this.errorSmsCode = false;
                    this.errorMessage = '';

                    if(!this.smsSended && !this.password)
                    {
                        this.errorPassword = true;
                        return;
                    }

                    if(!this.user && !this.smsSended)
                    {
                        if( this.password.length < 6 )
                        {
                            this.errorPassword = true;
                            this.errorMessage = '{{__('Min length of the password is 6 chars')}}';
                            return;
                        }

                        if(this.password != this.passwordConfirm)
                        {
                            this.errorPassword = true;
                            return;
                        }

                        this.sendSmsCode();
                        return;
                    }

                    if( this.smsSended && this.smsCode.length == 0)
                    {
                        this.errorSmsCode = true;
                        return;
                    }

                    if( this.recoveryPassword && ( (this.newPassword == '') || (this.newPassword.length < 6) )){
                        this.errorNewPassword = true;
                        this.errorMessage = '{{__('The New password field must contain more than 5 characters')}}';
                        return;
                    }

                    if( this.recoveryPassword && ( (this.passwordConfirm == '') || (this.passwordConfirm.length < 6) )){
                        this.errorConfirmPassword = true;
                        this.errorMessage = '{{__('The confirm password field must contain more than 5 characters')}}';
                        return;
                    }

                    if( this.recoveryPassword && (this.newPassword != this.passwordConfirm) ){
                        this.errorConfirmPassword = true;
                        this.errorMessage = '{{__('The New password and Confirm password fields must match')}}';
                        return;
                    }

                    var self = this;

                    // this.process = true;
                    axios.post('{{route('studentLogin')}}', {
                        login: this.phone,
                        sms_code: this.smsCode,
                        password: this.password,
                        new_password: this.newPassword,
                        register_fio: '{{ $register_fio ?? '' }}'
                    })
                        .then(function(response){

                            self.process = false;

                            if(!response.data.message)
                            {
                                location.href = '{{route("home")}}';
                            } else {
                                self.errorMessage = response.data.message;
                            }
                        });
                }
            }
        });
    </script>

@endsection