@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Login') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <h3>{{ __('Congratulations, you have successfully completed registration') }}!</h3>
                    <p><b>{{ __("What's next?") }}</b></p>
                    <ul>
                        <li>{{ __('Step 1 - Fill out') }} <a href={{route('userProfileID')}}>{{ __('student profile') }}</a></li>
                        <li>{{ __('Step 2 - Choose a preparation direction') }}</li>
                        <li>{{ __('Step 3 - Pay and get access to the selected program') }}</li>
                    </ul>

                    <p>{{ __('We wish you success and comfortable learning') }}.</p>
                    <a class="btn btn-info" href="{{route('userProfileID')}}">{{ __('To begin') }}</a>

                </div>
            </div>

        </div>
    </section>

@endsection

