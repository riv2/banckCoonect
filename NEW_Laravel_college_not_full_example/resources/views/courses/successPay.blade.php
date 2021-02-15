@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">
                    {{__('Courses')}}
                </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <h3> {{ __('You have successfully purchased the course') }} </h3>
                            <p>{{ __('The Name Of Course') }}: {{ $course->title ?? '' }} </p>
                            <p> {{ __('Course price') }}: {{ $course->cost ?? 0 }} </p>
                            <br />

                            <a href="{{ route('getCourseCabinet') }}">{{ __('Go to the office') }}</a>


                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
