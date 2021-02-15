@extends('layouts.app_old')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('Teacher profile page')}}</div>

                <div class="panel-body">
                    <blockquote>{{__("Warning! This document cannot be edited later. Please check all the fields carefully.")}}</blockquote>

                    <form class="form-horizontal" method="POST" action="{{ route('teacherProfileSave') }}" enctype="multipart/form-data">

                        {{ csrf_field() }}
                        @include('teacher.profile.edit.main')
                        <hr>
                        @include('teacher.profile.edit.education')

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{__("Approve")}}
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
