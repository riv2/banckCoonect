@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Enter phone number")}}</div>

                    <div class="panel-body">

                        <div class="col-md-9 col-md-offset-1" id="phoneForm">

                            <blockquote>{{__("Please enter a mobile number")}}</blockquote>

                            <form class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('mobile') ? ' has-error' : '' }}">
                                    <label for="mobile" class="col-md-4 control-label">{{__('Mobile phone')}}</label>
                                    <div class="input-group mb-2 col-md-4">
                                        <div class="input-group-addon">
                                            <span class="input-group-text">+7</span>
                                        </div>
                                        <input id="mobile" type="string" class="form-control" name="mobile" value="{{ old('mobile') }}" required autofocus minlength="7">
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('home_phone') ? ' has-error' : '' }}">
                                    <label for="home_phone" class="col-md-4 control-label">{{__('Home phone')}}</label>
                                    <div class="input-group mb-2 col-md-4">
                                        <div class="input-group-addon">
                                            <span class="input-group-text">+7</span>
                                        </div>
                                        <input id="home_phone" type="string" class="form-control" name="home_phone" value="{{ old('home_phone') }}" autofocus minlength="7">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-8 col-md-offset-4">
                                        <button id="sendPhone" type="button" class="btn btn-primary">
                                            {{__("Send")}}
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>

                        <div class="col-md-9 col-md-offset-1" id="approvePhone" style="display: none;">
                            <blockquote>{{__('We have sent you 4-digit number, please enter it below')}}</blockquote>
                            <form class="form-horizontal" method="post">
                                {{ csrf_field() }}


                                <div class="form-group{{ $errors->has('numbers') ? ' has-error' : '' }}">
                                    <div class="col-md-6">
                                        <input id="numbers" type="string" class="form-control" name="numbers" value="{{ old('numbers') }}" required autofocus minlength="4">
                                    </div>

                                </div>
                                <div class="alert hide alert-danger">{{ __('Invalid sms code') }}</div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <button id="sendCode" type="button" class="btn btn-primary">
                                            {{__("Send")}}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script type="text/javascript">
    window.onload = function(){


        $( "#sendPhone" ).click(function( event ) {
            event.preventDefault();

            var mobile = $('#mobile');
            var home_phone = $('#home_phone');

            var error = false;
            if(mobile.val().length < 7) {
                mobile.addClass('error');
                error = true;
            }

            if (error) return;

            $.ajax({
                url:'{{ route('teacherMirasEnterMobilePhoneSendcode') }}',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "mobile": mobile.val(),
                    "home_phone": home_phone.val()
                },
                type:'post',
                success:function(response){
                    var response = JSON.parse(response);
                    if(response.status == 'fail') {
                        alert(response.text);
                    } else {
                        $("#phoneForm").hide();
                        $("#approvePhone").show();
                    }
                }
            });

        });

        $( "#sendCode" ).click(function( event ) {
            event.preventDefault();

            var code = $('#numbers');

            var error = false;
            if(code.val().length == '') {
                code.addClass('error');
                error = true;
            }

            if (error) return;

            $.ajax({
                url:'{{ route('teacherMirasEnterMobilePhonePost') }}',
                data:{
                    "_token": "{{ csrf_token() }}",
                    "code": code.val()
                },
                type:'post',
                success:function(response){
                    if(response.status == 'fail') {
                        code.addClass('error');
                        $( "#approvePhone form .alert" ).removeClass('hide');
                        error = true;
                    } else {
                        window.location.href = response.redirect;
                    }
                }
            });

        });

        $('body').on('keypress', '.error', function() {
            $(this).removeClass('error');
        });

        $('#mobile').keyup(function(e){
            var ph = this.value.replace(/\D/g,'').substring(0,10);
            // Backspace and Delete keys
            var deleteKey = (e.keyCode == 8 || e.keyCode == 46);
            var len = ph.length;
            if(len==0){
                ph=ph;
            }else if(len<3){
                ph='('+ph;
            }else if(len==3){
                ph = '('+ph + (deleteKey ? '' : ') ');
            }else if(len<6){
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6);
            }else if(len==6){
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6)+ (deleteKey ? '' : '-');
            }else{
                ph='('+ph.substring(0,3)+') '+ph.substring(3,6)+'-'+ph.substring(6,10);
            }
            this.value = ph;
        });
    };

</script>