@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Specify a resume")}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_ENTER_RESUME_POST) }}">
                            {{ csrf_field() }}

                            <div class="field_all form-group">
                                <label for="resume_link" class="col-md-4 control-label">{{__('Link to resume')}}</label>
                                <div class="col-md-8">
                                    <input id="resume_link" type="text" class="form-control" name="resume_link" value="" autofocus>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('resume_file') ? ' has-error' : '' }}">
                                <label for="resume_file" class="col-md-4 control-label">{{__('Upload file')}}</label>
                                <div class="col-md-6">
                                    <label class="btn btn-xs btn-default btn-upload-file" for="resume_file">{{__('Selected file')}}</label>
                                    <input style="visibility:hidden;" id="resume_file" type="file" class="form-control" name="resume_file" value="" required autofocus>
                                    @if ($errors->has('resume_file'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('resume_file') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary" id="sendButton">
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
@endsection
