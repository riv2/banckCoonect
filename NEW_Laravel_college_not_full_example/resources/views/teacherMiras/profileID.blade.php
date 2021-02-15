@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("ID upload page")}}</div>

                    <div class="panel-body">
                        <blockquote>{{__("Please upload photo of your ID of both side.")}}</blockquote>

                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_USER_PROFILE_ID_POST ) }}">
                            {{ csrf_field() }}


                            <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                                <label for="front" class="col-md-4 control-label">{{__('Front side of ID')}}</label>

                                <div class="col-md-6">
                                    <label class="btn btn-xs btn-default btn-upload-file" for="front">{{__('Selected file')}}</label>
                                    <input style="visibility:hidden;" id="front" type="file" class="form-control" name="front" value="{{ old('front') }}" required autofocus>
                                    @if ($errors->has('front'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('front') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('back') ? ' has-error' : '' }}">
                                <label for="back" class="col-md-4 control-label">{{__('Back side of ID')}}</label>

                                <div class="col-md-6">
                                    <label class="btn btn-xs btn-default btn-upload-file" for="back">{{__('Selected file')}}</label>
                                    <input style="visibility:hidden;" id="back" type="file" class="form-control" name="back" value="{{ old('back') }}" required autofocus>
                                    @if ($errors->has('back'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('back') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <small class="col-md-6 col-md-offset-4">{{__("Photos must be in JPEG format and not exceed 5 Mb")}}</small>
                            <p>&nbsp;</p>

                            <label class="col-md-offset-1">{{__("Terms of use")}}:</label>

                            <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                                <div class="col-md-9 col-md-offset-1">
                                    <textarea readonly class="form-control agreement-text">{!! strip_tags(getcong('terms_conditions_description')) !!}</textarea>
                                </div>
                                <div class="col-md-9 col-md-offset-1">
                                    <input type="checkbox" name="agree" id="agree">
                                    <label for="agree" class="control-label">{{__("Accept")}}</label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" disabled="disabled" id="sendButton">
                                        {{__("Send")}}
                                    </button>
                                </div>
                            </div>
                        </form>

                        <small class="col-md-offset-1">{{__("No ID RK or enter manually? You need")}} <a href="{{ route('userProfileIDManual') }}">{{__("here")}}</a></small>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


<script type="text/javascript">
    window.onload = function () {

        $('#agree').click(function(){
            $('#sendButton').prop('disabled', function(i, v) { return !v; });
        });
    };
</script>
