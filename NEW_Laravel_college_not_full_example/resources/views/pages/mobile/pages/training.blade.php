@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Study') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">


                    <h4> {{ __('To enter the University press the button') }} <a class="btn btn-info btn-lg" href="{{ route('userProfileID') }}"> {{ __('Enrol') }} </a> </h4>


                </div>
            </div>

        </div>
    </section>

@endsection