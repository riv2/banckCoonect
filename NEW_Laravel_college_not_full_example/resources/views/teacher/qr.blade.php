@extends('layouts.app_old')

@section('title', 'QR')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">QR</div>

                    <div class="panel-body">
{{--                        <select class="form-control qr" id="discipline">--}}
{{--                            @foreach($disciplines as $discipline)--}}
{{--                                <option value="{{$discipline->id}}">{{$discipline->name}}</option>--}}
{{--                            @endforeach--}}
{{--                        </select>--}}
                        <input id="discipline" value="1" type="hidden">

                        <input v-on:click="start" type="button" class="btn btn-primary qr" value="{{__('Show QR')}}">

                        <div class="col-md-12 text-center">
                            <img class="qr" v-if="qrSrc" v-bind:src="qrSrc" style="width: 100%; object-fit: cover;" id="imgQR" />
                        </div>

                        <div class="col-md-12 text-center" v-cloak v-if="numeric_code" >
                            @lang('Numeric code'): <strong style="font-size:40px;" v-html="numeric_code"></strong>
                        </div>
                    </div>

{{--                    <div style="text-align: center;"><strong v-html="discipline_name"></strong></div>--}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        var app = new Vue({
            el: '#app',
            data: function () {
                return {
                    process: false,
                    qrSrc: '',
                    timer: null,
                    discipline_name : '',
                    numeric_code : ''
                };
            },
            methods: {
                generateQr: function () {
                    if (this.process) {
                        return ;
                    }

                    this.process = true;
                    axios.post('{{ route('teacherGetQRForTest') }}', {
                        "disciplineID": $('#discipline').val()
                    })
                        .then(response => {
                            if (response.data.success) {
                                this.qrSrc = response.data.qr;
                                this.discipline_name = response.data.discipline_name;
                                this.numeric_code = response.data.numeric_code;
                            } else {
                                alert(response.data.error);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.error = error
                        })
                        .finally(() => (this.process = false));
                },
                start: function() {
                    if (this.timer != null) {
                        clearTimeout(this.timer);
                        this.qrSrc = '';
                    }

                    this.generateQr();
                    this.timer = setInterval(this.generateQr, 10000);
                }
            },
            // created: function () {
            //     this.generateQr();
            //     this.timer = setInterval(this.generateQr, 5000);
            // }
        });
    </script>
@endsection