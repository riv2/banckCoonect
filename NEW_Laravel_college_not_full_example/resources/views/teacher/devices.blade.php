@extends('layouts.app_old')

@section('content')
    <div class="container" id="app">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Device list page')}}</div>

                    <div class="panel-body">
                        <div class="row">

                            <div class="col-xs-8 col-xs-offset-2">
                                <ul class="list-group">
                                    <li class="list-group-item" v-for="data in deviceList" class="row">
                                        <div class="col-xs-8">@{{data.name}} (@{{data.mac}})</div>

                                            <a class="btn btn-info" v-on:click="remove(data.id)">{{__('Remove device')}}</a>

                                    </li>
                                </ul>
                            </div>

                            <div class="col-md-4 text-center">
                                <button class="btn btn-info" v-on:click="getIp()">{{__('Add current device')}}</button>
                            </div>

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
                el: '#app',
                data: {
                    deviceList: []
                },
                mounted () {
                    this.getDeviceList();
                },
                methods: {
                    getDeviceList: function(){
                        axios.get('{{route('teacherGetDevices')}}')
                            .then(function(response){
                                response.data.map(function(value, key) {
                                    app.deviceList.push(value);
                                });
                            });
                    },
                    addNewDevice: function(ip){
                        axios.post('{{ route('teacherAddDevice') }}', {
                            ip: ip
                        }).then(function(response){
                            location.reload();
                        });
                    },
                    getIp: function(){
                        if(confirm('{{__('Are you sure you want to add current device?')}}')) {
                            axios.get('{{env('GET_MIRAS_LOCAL_IP_URL')}}')
                                .then(function(response){
                                    var ip = null;
                                    ip = response.data;
                                    ip = '192.168.20.16';
                                    if(ip) {
                                        app.addNewDevice(ip);
                                    }
                                })
                        }
                    },
                    remove: function(id){
                        if(confirm('{{__('Are you sure you want to remove device?')}}')) {
                            axios.post('{{ route('teacherDeleteDevice') }}', {
                                id: id
                            }).then(function(response){
                                location.reload();
                            })
                        }
                    }
                }
            })
    </script>
@endsection