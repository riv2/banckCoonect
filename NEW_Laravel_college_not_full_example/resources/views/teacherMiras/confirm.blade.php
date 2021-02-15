@extends('layouts.app')

@section('content')
    @if(!Session::has('flash_message'))
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                        <div class="panel-heading">{{__('Login')}}</div>

                        <div class="panel-body">
                            ￼
                            <h3>{{ __('Congratulations, you have successfully completed registration') }}!</h3>
                            <p><b>{{ __("What's next?") }}</b></p>
                            <ul>
                                <li>{{ __('Step 1 - Fill out') }} <a href={{route('userProfileID')}}>{{ __('teacher profile') }}</a></li>
                                <li>{{ __('Step 2 - indicate marital status') }}</li>
                                <li>{{ __('Step 3 - fill in the address') }}</li>
                                <li>{{ __('Step 4 - add phone') }}</li>
                                <li>{{ __('Step 5 - add resume') }}</li>
                                <li>{{ __('Step 6 - complete education') }}</li>
                                <li>{{ __('Step 7 - seniority') }}</li>
                            </ul>

                            <p>{{ __('We wish you success and comfortable learning') }}.</p>
                            <a class="btn btn-info" href="{{route('userProfileID')}}">{{ __('To begin') }}</a>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
