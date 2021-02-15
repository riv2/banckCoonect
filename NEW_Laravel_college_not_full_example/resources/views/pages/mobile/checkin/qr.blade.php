@extends('layouts.app')

@section('content')
    <div id="study-app" class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" style="padding-top: 20vh;">
                    <div class="panel-body">
                        <div v-if="!qrChecked" v-cloak class="text-center">
                            <div class="col-md-12">
                                <video muted autoplay playsinline v-bind:class="{'qr-error': error}" class="qr-video"></video>
                            </div>
                            <div class="col-md-12" v-if="deviceList.length > 2" v-on:click="changeDevice()">
                                <i class="fas fa-retweet" style="font-size: 3em;color: #ccc;margin-top: 5px;"></i>
                            </div>
                        </div>
                        <div v-if="qrChecked" class="text-center" v-cloak>
                            <span style="font-size: 6em;color: #00d600;"><i class="far fa-check-circle"></i></span>
                        </div>

                        <div v-if="numericError" v-cloak class="alert alert-danger">@{{numericError}}</div>

                        <div v-if="!qrChecked">
                            <p>@lang('Numeric code')</p>
                            <input
                                    type="tel"
                                    id="numeric_code"
                                    class="form-control numeric_code text-center margin-b15"
                                    maxlength="6"
                                    v-bind:class="{'qr-error': numericError}"
                                    @keypress="isNumber($event)"
                            >
                            <input type="button" value="@lang('Send')" v-on:click="sendNumericCode()" class="btn btn-info btn-lg btn-block numeric_code margin-b10">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/qcode-decoder.js" type="text/javascript"></script>
    <script type="text/javascript">
        var app = new Vue({
            el: '#study-app',
            data: function () {
                return {
                    timer: null,
                    qrValue: '',
                    qrDecoder: new QCodeDecoder(),
                    error: '',
                    deviceList: [],
                    currentDeviceIndex: null,
                    qrChecked: false,
                    numericError: ''
                };
            },
            methods: {
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
                    this.qrValue = res;
                    this.checkQr();
                },
                checkQr: function () {
                    var self = this;

                    if(!this.qrChecked) {
                        axios.post('{{ route('studentCheckinQrCheck') }}', {
                            code: this.qrValue
                        })
                            .then(response => {
                                if (response.data.message) {
                                    this.error = response.data.message;
                                } else {
                                    this.qrChecked = true;
                                    clearInterval(this.timer);
                                }
                            })
                            .catch(error => {
                                console.log(error);
                                this.error = error;
                            })
                            .finally(() => (this.process = false));
                    }
                },
                setDeviceList: function(){
                    var self = this;

                    if (navigator.mediaDevices !== undefined) {
                        navigator.mediaDevices.enumerateDevices()
                            .then(function (devices) {
                                devices.forEach(function (device) {
                                    if (device.kind == 'videoinput') {
                                        self.deviceList.push(device);
                                    }
                                });

                                self.streamCam();
                            })
                            .catch(function (err) {
                                console.log(err.name + ": " + err.message);
                            });
                    }
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
                },
                sendNumericCode : function () {
                    this.numericError = '';

                    if (!this.isNumericCodeValid()) {
                        return;
                    }

                    $('.numeric_code').prop('disabled', true);

                    axios.post('{{route('studentCheckinNumericCodeCheck')}}', {
                        code: $('#numeric_code').val()
                    })
                        .then(response => {
                            if (response.data.success) {
                                this.qrChecked = true;
                                clearInterval(this.timer);
                            } else {
                                this.numericError = response.data.error;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.numericError = error;
                        })
                        .finally(() => ($('.numeric_code').prop('disabled', false)));
                },
                isNumericCodeValid : function() {
                    if ($('#numeric_code').val().length != 6) {
                        // $('#numeric_code').parent().addClass('has-error');
                        $('#numeric_code').addClass('qr-error');
                        return false;
                    } else {
                        // $('#numeric_code').parent().removeClass('has-error');
                        $('#numeric_code').removeClass('qr-error');
                        return true;
                    }
                },
                isNumber: function(evt) {
                    evt = (evt) ? evt : window.event;
                    var charCode = (evt.which) ? evt.which : evt.keyCode;
                    if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                        evt.preventDefault();
                    } else {
                        return true;
                    }
                }
            },
            mounted: function(){
                this.scanQR();
            }
        });
    </script>
@endsection