@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Fitness room')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">


                            <h3>{{ __('Work schedule') }}</h3>

                            <p> {{ __('For students of Miras University - from 12.00 to 17.00 on weekdays according to the schedule of classes') }} </p>

                            <p>{{ __('By subscription - from 8.00 to 22.00 seven days a week') }} </p>


                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

