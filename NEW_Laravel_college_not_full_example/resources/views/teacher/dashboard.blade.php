@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('Profile') }}</div>

                    <div class="panel-body">
                        <div class="row">

                            <div class="col-md-12">
                                <center>
                                    <p>
                                        <a href="profile">{{ __('Profile') }}</a> &nbsp;|&nbsp;
                                        <a href="profile/edit">{{ __('Edit profile') }}</a> &nbsp;|&nbsp;
                                    </p>
                                </center>
                                <p></p><br><hr>
                                <center>
                                    <p>
                                        <a href="courses/list">{{ __('My courses') }}</a> &nbsp;|&nbsp;
                                        <a href="courses/add">{{ __('Add course') }}</a>
                                    </p>
                                </center>
                                <p></p><br><hr>
                                <center><p>{{ __('My student') }}</p></center>
                                <p>Курс 2</p><br><hr>
                            </div>

                            <div class="col-md-8 col-md-offset-2">

                            </div>

                            {{--                            @if($user->usertype == 'Teacher')--}}
                            <div class="col-md-4 text-center">
                                <a class="btn btn-primary" href="{{ url('teacher/courses/add') }}">{{ __('Add course') }}</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
