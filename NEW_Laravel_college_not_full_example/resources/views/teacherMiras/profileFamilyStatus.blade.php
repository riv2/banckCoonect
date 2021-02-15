@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__("Indicate marital status")}}</div>

                    <div class="panel-body">
                        <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route( \App\Teacher\ProfileTeacher::REGISTRATION_STEP_FAMILY_STATUS_POST) }}">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label for="front" class="col-md-4 control-label">{{__('Family status')}}</label>
                                <div class="col-md-6">
                                    <select name="family_status" class="form-control">
                                        <option value="{{ \App\Teacher\ProfileTeacher::FAMILY_STATUS_SINGLE }}">{{__(\App\Teacher\ProfileTeacher::FAMILY_STATUS_SINGLE)}}</option>
                                        <option value="{{ \App\Teacher\ProfileTeacher::FAMILY_STATUS_MARITAL }}">{{__(\App\Teacher\ProfileTeacher::FAMILY_STATUS_MARITAL)}}</option>
                                    </select>
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
