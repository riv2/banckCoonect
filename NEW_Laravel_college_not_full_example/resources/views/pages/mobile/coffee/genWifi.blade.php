@extends('layouts.app')

@section('content')

    <section class="content" id="wifi-app">
        <div class="container-fluid">


            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10" style="text-align: center;">
                            <div id="qrwifi" class="col-12">
                                <div><img v-bind:src="qr" /></div>
                                <h2 v-text="wifiCode"></h2>
                            </div>

                            <div class="row banners-bottom-menu no-margin">
                                <div class="col-12 item">
                                    <a v-on:click="genWifi()">
                                        <p class="text-danger font-size12">{{ __('generate Wifi') }}</p>
                                    </a>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-1"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>


@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#wifi-app',
            data: {
                loader: false,
                qr: null,
                wifiCode: null
            },
            methods: {
                genWifi: function(tarifId = null){
                        app.loader = true;
                        var url = '{{route("genWifi")}}';
                        axios.get(url)
                            .then(function(response){

                                app.qr = response.data.qr;
                                app.wifiCode = response.data.numeric_code;

                            }).catch(function(error){
                            //app.loader = false;
                        });
                }

            }
        });


    </script>
@endsection

@endsection