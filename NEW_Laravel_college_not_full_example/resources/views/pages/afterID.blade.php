@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-form">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Choosing a field of study')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div class="row">

                                <div class="col-12 alert alert-success margin-b20">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                    {{ __('Your identification document is accepted, now you need to choose the direction of study') }}.
                                </div>


                                <div class="col-12 row text-center">
                                    <div class="col-12 col-sm-2">
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <a class="btn btn-info font-size10 margin-b15" href="{{route(\App\Services\StepByStep::firstRoute('bc_application'), ['application' => 'bachelor'])}}">{{ __('Undergraduate') }}</a>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <a class="btn btn-info font-size10 margin-b15" href="{{route(\App\Services\StepByStep::firstRoute('mg_application'), ['application' => 'master'])}}">{{ __("Master's") }}</a>
                                    </div>
                                    <div class="col-12 col-sm-2">
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

