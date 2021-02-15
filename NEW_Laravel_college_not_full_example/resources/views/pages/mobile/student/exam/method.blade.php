@extends('layouts.app')

@section('title', __('Selecting exam method'))

@section('content')
    <script src="/js/qcode-decoder.js" type="text/javascript"></script>

    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Selecting exam method')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <h4>@lang('Please choose a method of exam in the discipline') "{{$SD->discipline->name}}"</h4>

                    <div class="margin-b20">
                        <input type="button" value="@lang('Take an audience test')" class="btn btn-info btn-lg btn-block" v-on:click="startChecking()">
                        @if(false)
                        <p class="text-center"> @lang('or') </p>
                        <a role="button" class="btn btn-info btn-lg btn-block" href="{{route('remoteAccessPay', ['id' => $SD->discipline->id, 'test' => 'exam'])}}">@lang('Take the test remotely')</a>
                        @endif
                    </div>

                    <div class="row margin-15 padding-20">
                        <div class="col-12">
                            <div v-if="checkingProcess" v-cloak class="text-center">
                                <div class="col-12 margin-b15">
                                    <video muted autoplay playsinline v-bind:class="{'qr-error': error}" class="qr-video"></video>
                                </div>
                                <div class="col-12 margin-b15" v-if="deviceList.length > 2" v-on:click="changeDevice()">
                                    <i class="fas fa-retweet" style="font-size: 3em;color: #ccc;margin-top: 5px;"></i>
                                </div>
                                <div v-if="qrValue" v-cloak class="alert alert-info">
                                    @{{qrValue}}
                                </div>
                                <div v-if="error" v-cloak class="alert alert-danger">@{{ error }}</div>
                                <div v-if="numericError" v-cloak class="alert alert-danger">@{{ numericError }}</div>

                                <div>
                                    <p>@lang('Numeric code') </p>
                                    <input type="tel" id="numeric_code" class="form-control numeric_code text-center margin-b15" maxlength="6" v-bind:class="{'qr-error': numericError}">
                                    <input type="button" value="@lang('Send')" v-on:click="sendNumericCode()" class="btn btn-info btn-lg btn-block numeric_code margin-b10">
                                </div>
                            </div>
                            <div v-if="qrChecked" class="text-center" v-cloak>
                                <span style="font-size: 6em;color: #00d600;"><i class="far fa-check-circle"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">
        const DISCIPLINE_ID = '{{$SD->discipline->id}}';

        var app = new Vue({
            el: '#study-app',
            data: function () {
                return {
                    timer: null,
                    qrValue: '',
                    qrDecoder: new QCodeDecoder(),
                    error: '',
                    numericError: '',
                    deviceList: [],
                    currentDeviceIndex: null,
                    qrChecked: false,
                    checkingProcess : false
                };
            },
            methods: {
                startChecking : function() {
                    if (!this.checkingProcess) {
                        this.checkingProcess = true;
                        this.scanQR();
                    }
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
                    this.qrValue = res;
                    this.checkQr();
                },
                checkQr: function () {
                    var self = this;

                    axios.post('{{route('studentExamQRCheck')}}', {
                        code: this.qrValue,
                        discipline_id: DISCIPLINE_ID
                    })
                        .then(response => {
                            if (response.data.status) {
                                location.href = '{{route('studentExam', ['id' => $SD->discipline->id])}}';
                            } else {
                                this.error = response.data.error;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error;
                        })
                        .finally(() => (this.process = false));
                },
                setDeviceList: function() {
                    var self = this;
                    navigator.mediaDevices.enumerateDevices()
                        .then(function(devices) {
                            devices.forEach(function(device) {
                                if (device.kind == 'videoinput') {
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
                    this.timer = setInterval(this.scanCode, 1000);
                },
                changeDevice: function() {
                    if(this.currentDeviceIndex === null) {
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

                    axios.post('{{route('studentExamNumericCodeCheck')}}', {
                        code: $('#numeric_code').val(),
                        discipline_id: DISCIPLINE_ID
                    })
                        .then(response => {
                            if (response.data.status) {
                                location.href = '{{route('studentExam', ['id' => $SD->discipline->id])}}';
                            }
                            else {
                                this.numericError = response.data.error;
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.numericError = error;
                        })
                        .finally(() => ($('.numeric_code').prop('disabled', false)));
                },
                isNumericCodeValid : function () {
                    if ($('#numeric_code').val().length != 6) {
                        // $('#numeric_code').parent().addClass('has-error');
                        $('#numeric_code').addClass('qr-error');
                        return false;
                    } else {
                        // $('#numeric_code').parent().removeClass('has-error');
                        $('#numeric_code').removeClass('qr-error');
                        return true;
                    }
                }
            }
        });
    </script>
@endsection