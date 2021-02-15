@extends('layouts.app')

@section('content')

    <section class="content" id="qr-enter-app">
        <div class="container-fluid">


            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10" style="text-align: center;">
                            <div id="qrwifi" class="col-12">
                                <div><img v-bind:src="qr" /></div>
                            </div>

                            <div class="row banners-bottom-menu no-margin">
                                <div class="col-12 item">
                                    <a v-on:click="gen()">
                                        <p class="text-danger font-size12">{{ __('generate code') }}</p>
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
            el: '#qr-enter-app',
            data: {
                loader: false,
                qr: null
            },
            methods: {
                gen: function(tarifId = null){
                        this.loader = true;
                        var url = '{{route("getEnterQR")}}';
                        axios.get(url)
                            .then(function(response){

                                app.qr = response.data.qr;
                                app.wifiCode = response.data.numeric_code;

                            }).catch(function(error){
                            //app.loader = false;
                        });
                }

            },
            mounted: function(){
                this.gen();
            },
            created() {
                this.interval = setInterval(() => app.gen(), 5000);
            }
        });


    </script>
@endsection

@endsection