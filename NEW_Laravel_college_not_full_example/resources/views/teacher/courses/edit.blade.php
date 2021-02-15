@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Courses')}}</div>

                    <div class="panel-body">
                        @include('teacher.courses.list')

                        @if($course->status == \App\Course::STATUS_ACTIVE &&
                            Auth::user()->teacherProfile->status == \App\Teacher\ProfileTeacher::STATUS_ACTIVE
                        )
                            @include('teacher.courses.tabs')
                        @endif

                        @if(isset($courseTab) && $courseTab == true)
                            @include('teacher.courses.main_tab.edit')
                        @elseif(isset($lecturesTab) && $lecturesTab == true)
                            @include('teacher.courses.lectures_tab.edit')
                        @elseif(isset($scheduleTab) && $scheduleTab == true)
                            @include('teacher.courses.schedule_tab.list')
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
