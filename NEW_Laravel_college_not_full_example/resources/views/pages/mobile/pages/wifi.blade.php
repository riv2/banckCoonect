@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="wifi-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Wifi')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div v-if="alreadyHadWifi === false">
                                <p>{{__('Welcome to MIRAS.EDUCATION')}}</p>
                                <p>{{__('You can access Internet resources by connecting to the MirasAPP interactive site')}}</p>
                                <p>{{__('For your convenience, a variety of connection packages')}}</p>

                                <p>{{__('To connect to unlimited access, you must')}}:</p>
                                <ol>
                                    <li>{{__('Connect to the wi-fi miras.app network')}}</li>
                                    <li>{{__('Sign in again')}}</li>
                                    <li>{{__('Select the connection package you are interested in')}}</li>
                                    <li>{{__('Make a payment from the card of any bank or bonus account')}}</li>
                                </ol>
                                
                            </div>
                            <div v-if="alreadyHadWifi === true">
                                <p>
                                    {{ __("To access Wi-Fi, you need to connect to the Miras network and activate the necessary package. Miras.app and WhatsApp are available for free on the Miras network") }}
                                </p>
                            </div>


                            <div v-html="message" v-bind:class="{'alert': message, 'alert-error': hasError, 'alert-success': !hasError}"></div>

                            <div id="loader-layout" style="position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.5);text-align: center;" v-if="loader"><img src="{{ URL::to('assets/img/load.gif') }}" style="opacity: 0.5; max-width: 100px;"></div>

                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ __('Package') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if( isset(Auth::User()->studentProfile->iin) )
                                    @foreach($wifiTariff as $tariff)
                                        <tr>
                                            <td>{{ $tariff->name }}</td>
                                            <td>
                                                @if($tariff->active)
                                                    <span>{{ $tariff->active->code }}</span>
                                                @else
                                                    <button class="btn btn-info btn-flat" type="button" v-on:click="activateShowModal({{ $tariff->id }})">
                                                        {{ __('Activate') }}
                                                        <span v-if="alreadyHadWifi === false">{{ __('for free') }}</span>
                                                        <span v-if="alreadyHadWifi === true">{{ $tariff->cost }} тг</span>

                                                    </button>

                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>{{ __('Use vaucher') }}</td>
                                    <td>
                                        {{--<button class="btn btn-info btn-flat" type="button" v-on:click="showModal()"> QR</button>--}}
                                        <input type="text" name="code" class="form-control" style="float: left; width: 130px;" v-model="inputCode">
                                        <button class="btn btn-info btn-flat" type="button" v-on:click="pay()"> {{ __('Use') }}</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <div class="modal" tabindex="-1" role="dialog" id="QrModal">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{__('QR wifi voucher scanner')}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div v-if="!qrChecked" v-cloak class="text-center">
                                                <div class="col-md-12">
                                                    <video muted autoplay playsinline v-bind:class="{'qr-error': error}" class="qr-video"></video>
                                                </div>
                                                <div class="col-md-12" v-if="deviceList.length > 2" v-on:click="changeDevice()">
                                                    <i class="fas fa-retweet" style="font-size: 3em;color: #ccc;margin-top: 5px;"></i>
                                                </div>
                                                <div v-if="qrValue" v-cloak class="alert alert-info">
                                                    @{{qrValue}}
                                                </div>
                                                <div v-if="error" v-cloak class="alert alert-danger">@{{ error }}</div>
                                            </div>
                                            <div v-if="qrChecked" class="text-center" v-cloak>
                                                <span style="font-size: 6em;color: #00d600;"><i class="far fa-check-circle"></i></span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal" tabindex="-1" role="dialog" id="activateModal">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{__('Activate package?')}}</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <button class="btn btn-info btn-flat" type="button" v-on:click="pay()" data-dismiss="modal"> {{ __('Yes') }}</button>
                                            <button class="btn btn-secondary btn-flat" type="button" data-dismiss="modal"> {{ __('No') }}</button>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <video class="col-12 margin-t20 margin-b20" loop autoplay playsinline controls="true" width="100%" height="100%" muted="muted" preload="true" poster="{{ URL::asset('assets/video/miras_wifi.jpg') }}">
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.mov" type="video/mov"/>
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.mp4" type="video/mp4" />
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.ogg" type="video/oog" />
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script src="/js/qcode-decoder.js" type="text/javascript"></script>
    <script type="text/javascript">
        var app = new Vue({
            el: '#wifi-app',
            data: {
                wifiSendRequest: false,
                loader: false,
                message: null,
                hasError: false,
                inputCode: null,
                timer: null,
                qrValue: '',
                qrDecoder: new QCodeDecoder(),
                error: '',
                deviceList: [],
                currentDeviceIndex: null,
                qrChecked: false,
                tarifId: null,
                alreadyHadWifi: null,
                ip: null
            },
            methods: {
                pay: function(tarifId = null){
                    tarifId = app.tarifId;
                    app.loader = true;
                    //app.ip = '192.168.20.18';
                    if(app.ip) {
                        if(tarifId) {
                            app.allowWifi(tarifId);
                        } else {
                            app.allowWifi(tarifId, app.inputCode);
                        }
                    } else {
                        app.hasError = true;
                        app.loader = false;
                        app.message = "{{__("You should be connected to Miras Wifi network")}}";
                    }

                },
                allowWifi: function(tarifId, code = null){
                    axios.post('{{route('userAllowWifi')}}', {
                        ip: app.ip,
                        tarifId: tarifId,
                        code: code
                    })
                        .then(function(response){
                            if(response.data.status == true) {
                                app.hasError = false;
                            } else {
                                app.hasError = true;
                            }
                            app.message = response.data.message;

                            app.loader = false;
                        });
                },
                showModal: function() {
                    $('#QrModal').modal('show');
                    app.scanQR();
                },
                activateShowModal: function(tarifId) {
                    app.tarifId = tarifId;
                    $('#activateModal').modal('show');
                },
                getIP: function(){
                    axios.get('{{env('GET_MIRAS_LOCAL_IP_URL')}}')
                        .then(function(response){
                            app.ip = response.data;
                            app.checkAlreadyHadWifi();

                            if(1!=1) {
                                app.hasError = true;
                                app.message = "{{__("You should be connected to Miras Wifi network")}}";
                                app.loader = false;
                            }
                        })
                        .catch(function(err) {
                            //console.log(err.name + ": " + err.message);
                            app.hasError = true;
                            app.message = "{{__("You should be connected to Miras Wifi network")}}";
                            app.loader = false;
                        });

                },
                checkAlreadyHadWifi: function() {
                    axios.get('{{route('alreadyHadWifi')}}', {
                        ip: app.ip
                    })
                        .then(function(response) {
                            app.alreadyHadWifi = response.data.status;
                            app.loader = false;
                        });
                },
                streamCam: function () {

                    if (window.video) {
                        window.video.srcObject.getTracks().forEach(track => {
                            track.stop();
                        });
                    }

                    window.video = document.querySelector('video');

                    if (navigator.mediaDevices !== undefined && navigator.mediaDevices.getUserMedia) {
                        function successCallback(stream) {
                            window.video.srcObject = stream;
                            window.video.play();
                        }

                        function errorCallback(error) {
                            this.$emit('videoError', error);
                        }

                        if(this.currentDeviceIndex === null) {
                            navigator.mediaDevices.getUserMedia({
                                video: { /*width: {ideal: 320}, height: {ideal: 320},*/ facingMode: "environment"},
                                audio: false
                            })
                                .then(successCallback)
                                .catch(errorCallback);
                        } else {
                            navigator.mediaDevices.getUserMedia({
                                //video: { /*width: {ideal: 320}, height: {ideal: 320},*/ facingMode: "environment"},
                                video: {deviceId: this.deviceList[this.currentDeviceIndex].deviceId },
                                audio: false
                            })
                                .then(successCallback)
                                .catch(errorCallback);
                        }
                    } else {
                        navigator.getMedia = (
                            navigator.getUserMedia ||
                            navigator.webkitGetUserMedia ||
                            navigator.mozGetUserMedia ||
                            navigator.msGetUserMedia
                        );

                        if (navigator.getMedia) {
                            navigator.getMedia({
                                    video: {width: 250, height: 250, facingMode: "environment"},
                                    audio: false
                                },
                                function (stream) {
                                    if (navigator.mozGetUserMedia) {
                                        window.video.mozSrcObject = stream;
                                    } else {
                                        window.video.srcObject = stream;
                                    }
                                    window.video.play();
                                },
                                function (err) {
                                    this.$emit('videoError', err);
                                }
                            );
                        }
                    }
                },
                scanCode: function () {
                    if (!this.error) {

                        var self = this;

                        this.qrDecoder.decodeFromVideo(window.video, function (er, res) {
                            self.$emit('qrDecoded', res);
                        });
                    }
                },
                videoError: function (err) {
                    this.error = err;
                    console.log(err);

                },
                qrDecoded: function (res) {
                    if(res !== undefined && res.length > 1 ){
                        app.inputCode = res;
                        $('#QrModal').modal('hide');
                    }
                    //app.pay();
                    /*
                    $('#QrModal').on('hidden.bs.modal', function () {
	                  app.pay();
                    });
                    */


                },
                setDeviceList: function(){
                    var self = this;
                    navigator.mediaDevices.enumerateDevices()
                        .then(function(devices) {
                            devices.forEach(function(device) {
                                if(device.kind == 'videoinput')
                                {
                                    self.deviceList.push(device);
                                }
                            });

                            self.streamCam();
                        })
                        .catch(function(err) {
                            console.log(err.name + ": " + err.message);
                        });
                },
                scanQR: function () {
                    this.$on('videoError', this.videoError);
                    this.$on('qrDecoded', this.qrDecoded);
                    this.setDeviceList();
                    //this.streamCam();
                    this.timer = setInterval(this.scanCode, 1000);
                },
                changeDevice: function() {

                    if(this.currentDeviceIndex === null)
                    {
                        this.currentDeviceIndex = 0;
                    } else {
                        if((this.currentDeviceIndex + 1) < this.deviceList.length) {
                            this.currentDeviceIndex++;
                        } else {
                            this.currentDeviceIndex = 0;
                        }
                    }

                    this.streamCam();
                }

            },
            mounted: function(){
                //this.scanQR();
                this.loader = true;
                //app.getIP();
            },
            created() {
                this.interval = setInterval(() => app.getIP(), 5000);
            }
        });


    </script>
@endsection
