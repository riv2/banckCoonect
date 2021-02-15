@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Student panel page')}}</div>
                    <div class="panel-body">
                        {{ __('Data successfully saved and sent for processing.') }}<br>
                        {{ __('You have chosen training') }} &nbsp; {{ \App\Services\Auth::user()->studentProfile->speciality->name }}.
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection