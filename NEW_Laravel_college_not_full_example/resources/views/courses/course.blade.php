@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">
                {{ $course->title ?? '' }}
            </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            @if( $course->inner_photo && file_exists( public_path('/images/uploads/courses/' . $course->inner_photo)) )
                                <img class="rounded float-right margin-15 shadow-sm p-3 mb-5 bg-white rounded" style="max-height:300px;" src="/images/uploads/courses/{{ $course->inner_photo }}" />
                            @endif

                            <p> {{ __('Form of the course') }}: {{ __($course->form_holding) }} </p>

                            <p> {{ __('Volume in hours') }}: {{ $course->hours ?? 0 }}  </p>

                            <p> {{ __('Course description') }}: {{ $course->getField('description') ?? '' }} </p>

                            @if( $course->trial_course_file && file_exists( public_path('/images/uploads/courses/' . $course->trial_course_file)) )
                            <p> {{ __('Free trial lesson') }}: <a href="/images/uploads/courses/{{ $course->trial_course_file }}" target="_blank"> {{ __('View the file') }} </a> </p>
                            @endif

                            @if( $course->trial_course_link )
                            <p> {{ __('Free trial lesson') }}: <a href="{{ $course->trial_course_link }}" target="_blank"> {{ __('See') }} </a> </p>
                            @endif

                            <p> {{ __('Course participant') }}: {{ $course->training_group ?? 0 }}  </p>

                            <p> {{ __('Certificate availability') }}: {{
                            ( !empty($course->is_certificate) && ($course->is_certificate == \App\Course::IS_CERTIFICATE_YES) ) ? __(\App\Course::IS_CERTIFICATE_YES) : __(\App\Course::IS_CERTIFICATE_NO)
                            }}  </p>

                            @if( $course->certificate_file_name && file_exists( public_path('/images/uploads/certificates/' . $course->certificate_file_name)) )
                                <p> {{ __('Certificate') }}: <a href="/images/uploads/certificates/{{ $course->certificate_file_name }}" target="_blank"> {{ __('View the file') }} </a> </p>
                            @endif

                            @if( $course->scheme_courses_file && file_exists( public_path('/images/uploads/certificates/' . $course->scheme_courses_file)) )
                                <p> {{ __('Thematic plan of the course with a description of the results') }}: <a href="/images/uploads/certificates/{{ $course->scheme_courses_file }}" target="_blank"> {{ __('View the file') }} </a> </p>
                            @endif

                            @if( $course->scheme_courses_link )
                                <p> {{ __('Thematic plan of the course with a description of the results') }}: <a href="{{ $course->scheme_courses_link }}" target="_blank"> {{ __('See') }} </a> </p>
                            @endif

                            @if( $course->author_resume_file && file_exists( public_path('images/uploads/courses' . $course->author_resume_file)) )
                                <p> {{ __('Author with a brief summary (file)') }}: <a href="/images/uploads/courses/{{ $course->author_resume_file }}" target="_blank"> {{ __('View the file') }} </a> </p>
                            @endif

                            @if( $course->author_resume_link )
                                <p> {{ __('Author with a brief summary (link)') }}: <a href="http://{{ $course->author_resume_link }}" target="_blank"> {{ __('See') }} </a> </p>
                            @endif


                            <hr class="clearfix" />

                            @if( empty($courseStudent) )
                                <a class="btn btn-info" href="{{ route('getCourseInfo',['course'=>$course->id]) }}"> {{ __('Sign up for a course') }} </a>
                            @else
                                <p><strong> {{ __('The course is already purchased') }}! </strong></p>
                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection