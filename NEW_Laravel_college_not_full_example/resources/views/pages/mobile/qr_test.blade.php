@extends('layouts.app')

@section('content')
    <div id="study-app" class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">

                    <div class="panel-body" style="padding-top: 20vh;">
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
                    qrChecked: false
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
                    //this.checkQr();
                },
                checkQr: function () {
                    var self = this;

                    axios.post('/api/qr/check', {
                        code: this.qrValue
                    })
                        .then(response => {
                            this.$session.set('qrcode', true);
                            location.href = app.backUrl;
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.process = false));
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
                this.scanQR();
            }
        });
    </script>
@endsection