@extends('layouts.app')

@section('content')
    @if(!Session::has('flash_message'))
        <div class="container">

            <h2>{{ __('In the development') }}</h2>

        </div>
    @endif
@endsection
