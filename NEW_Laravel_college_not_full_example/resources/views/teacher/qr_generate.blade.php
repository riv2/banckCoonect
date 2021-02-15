@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="col-md-12 text-center">
                            <img class="qr" src="" style="width: 100%; object-fit: cover;" />
                        </div>

                        <div class="col-md-12 text-center">
                            @lang('Numeric code'): <strong style="font-size:40px;" id="numeric_code"></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    /*var app = new Vue({
        el: '#app',
        data: function () {
            return {
                process: false,
                qrSrc: '',
                timer: null
            };
        },
        methods: {
            generateQr: function () {
                this.process = true;
                axios.post('{{ route('teacherQrGenerate') }}', {})
                    .then(response => {
                        this.qrSrc = response.data.qr;
                    })
                    .catch(error => {
                        console.log(error);
                        this.error = error
                    })
                    .finally(() => (this.process = false));
            }
        },
        created: function () {
            this.generateQr();
            this.timer = setInterval(this.generateQr, 5000);
        }
    });*/

    var generateQr = function () {
        $.ajax({
            url: "{{route('teacherQrGenerate')}}",
            method: "POST",
            data: {"_token": "{{csrf_token()}}"},
            success: function (data) {
                if (data.success) {
                    $('img.qr').attr('src', data.qr);
                    $('#numeric_code').text(data.numeric_code);
                } else {
                    alert(data.error);
                }
            }
        });
    };

    generateQr();

    var timer = setInterval(generateQr, 8000);

</script>
@endsection