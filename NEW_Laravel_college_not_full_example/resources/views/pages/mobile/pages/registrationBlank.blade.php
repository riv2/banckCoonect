@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="alert alert-warning">
                    {!! __('It is necessary to complete the registration of personal data')!!}  <a href="{{ route('home') }}"> {{__('Continue')}} </a>
                </div>
            </div>
        </div>
    </div>
@endsection