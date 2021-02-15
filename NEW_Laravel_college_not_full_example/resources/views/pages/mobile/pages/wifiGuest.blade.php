@extends('layouts.captive_portal')

@section('content')

    <section class="content">
        <div id="wifi-app">
            <div class="captive-bg">
                <div class="card-body">

                    <div class="row" style="font-size: 120%;">
                        <div class="col-xs-3 col-md-3"></div>
                        <div class="col-xs-6 col-md-6">

                            <h1>{{__('Welcome to MIRAS.EDUCATION')}}</h1>
                            <p>{{__('You can access Internet resources by connecting to the MirasAPP interactive site')}}</p>
                            <p>{{__('For your convenience, a variety of connection packages')}}</p>

                            <p>{{__('To connect to unlimited access, you must')}}:</p>
                                
                            
                            <ol>
                                <li>{{__('Make a')}}
                                    <a href="{{route('register')}}">{{__('registration')}}</a>, {{__('or')}}
                                    <a href="{{route('login')}}">{{__('login')}}</a>
                                    {{__('web-site')}}
                                    <a href="{{route('home')}}">MIRAS.APP</a>
                                </li>
                                <li>{{__('Select the connection package you are interested in')}}</li>
                                <li>{{__('Make a payment from the card of any bank or bonus account')}}</li>
                                <li>{{__('Activate the action of the package using the received code')}}</li>
                            </ol>

                            <p v-if="alreadyHadWifi != true">
                                {{__('First connection Free')}}
                            <span class="btn btn-info" v-on:click="order()">{{__('Connect')}}</span>
                            </p>

                            <p>{{__('By purchasing an unlimited Internet package you get access to all points of wi-fi miras.app in the city')}}</p>


                            <video width="100%" controls="controls" preload="true" poster="{{ URL::asset('assets/video/miras_wifi.jpg') }}">
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.mov" type="video/mov"/>
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.mp4" type="video/mp4" />
                                <source src="https://assets.object.pscloud.io/video/miras_wifi.ogg" type="video/oog" />
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        <div class="col-xs-3 col-md-3"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#wifi-app',
            data: {
                message: null,
                hasError: false,
                alreadyHadWifi: true,
                ip: null
            },
            methods: {
                getIP: function(){
                    axios.get('{{env('GET_MIRAS_LOCAL_IP_URL')}}')
                        .then(function(response){
                            app.ip = response.data;
                            //ip = '192.168.20.18';
                            if(app.ip) {
                                app.checkAlreadyHadWifi();
                            } else {
                                app.hasError = true;
                                app.message = "{{__("You should be connected to Miras Wifi network")}}";
                            }
                        }).catch(function(error){
                        app.hasError = true;
                        app.message = "{{__("You should be connected to Miras Wifi network")}}";
                    });

                },
                checkAlreadyHadWifi: function() {
                    axios.get('{{route('alreadyHadWifi')}}', {
                        ip: app.ip
                    })
                        .then(function(response) {
                            app.alreadyHadWifi = response.data.status;
                        });
                },
                order: function() {
                    window.location.href = '{{route("login")}}';
                }

            },
            mounted: function(){
                this.getIP();
            }
        });


    </script>
@endsection


<style type="text/css">
    .captive-bg h1 {color: #333;}
    .captive-bg {
        color: #333;
        height: 100%;
        
        
    }
</style>
