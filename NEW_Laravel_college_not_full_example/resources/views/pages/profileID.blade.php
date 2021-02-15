@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('ID upload page')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <h4>{{__("Please upload photo of your ID of both side.")}}</h4>

                            <form class="col-12" method="POST" enctype="multipart/form-data" action="{{ route('userProfileIDPost') }}">

                                {{ csrf_field() }}


                                <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                                    <label for="front" class="control-label">{{__('Front side of ID')}}</label>

                                    <div class="col-md-6">
                                        <div class="col-md-12 col-xs-6 no-padding-left" id="photo-front-block">
                                            <img id="photo-front" style="width: 100%; margin-bottom: 10px; display: none;" />
                                        </div>
                                        <div class="col-md-12 col-xs-12 no-padding-left">
                                            <label class="btn btn-xs btn-default btn-upload-file" for="front">{{__('Selected file')}}</label>
                                            <input style="visibility:hidden;" id="front" type="file" class="form-control" name="front" value="{{ old('front') }}" required autofocus>
                                        </div>

                                        @if ($errors->has('front'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('front') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group{{ $errors->has('back') ? ' has-error' : '' }}">
                                    <label for="back" class="control-label">{{__('Back side of ID')}}</label>

                                    <div class="col-md-6">

                                        <div class="col-md-12 col-xs-6 no-padding-left">
                                            <img id="photo-back" style="width: 100%; margin-bottom: 10px; display: none;" />
                                        </div>

                                        <div class="col-md-12 col-xs-12 no-padding-left">
                                            <label class="btn btn-xs btn-default btn-upload-file" for="back">{{__('Selected file')}}</label>
                                            <input style="visibility:hidden;" id="back" type="file" class="form-control" name="back" value="{{ old('back') }}" required autofocus>
                                        </div>

                                        @if ($errors->has('back'))
                                            <span class="help-block">
                                        <strong>{{ $errors->first('back') }}</strong>
                                    </span>
                                        @endif
                                    </div>
                                </div>

                                <small class="col-12">{{__("Photos must be in JPEG format and not exceed 5 Mb")}}</small>
                                <p>&nbsp;</p>

                                <div id="terms">
                                    <label>{{__("Terms of use")}}:</label>

                                    <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                                        <div class="col-12">
                                            <textarea rows="5" readonly class="form-control agreement-text">{!! strip_tags(getcong('terms_conditions_description')) !!}</textarea>
                                        </div>
                                        <div class="col-12">
                                            <input type="checkbox" name="agree" id="agree">
                                            <label for="agree" class="control-label">{{__("Accept")}}</label>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-info" disabled="disabled" id="sendButton">
                                                {{__("Send")}}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <small>{{__("No ID RK or enter manually? You need")}} <a href="{{ route('userProfileIDManual') }}">{{__("here")}}</a></small>

                        </div>
                        <div class="col-md-2"></div>
                    </div>


                </div>
            </div>

        </div>
    </section>

@endsection

@section('scripts')
    <script type="text/javascript">

        function readURL(input, imgId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + imgId).attr('src', e.target.result);
                    $('#' + imgId).css('display', 'block');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        window.onload = function () {

            $('#agree').click(function(){
                $('#sendButton').prop('disabled', function(i, v) { return !v; });
            });

            $('#front').change(function () {
                readURL(this, 'photo-front');
            });

            $('#back').change(function () {
                readURL(this, 'photo-back');
            });
        };

    </script>
@endsection

