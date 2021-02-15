@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Pool')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <h3>
                                {{ __('Working hours:') }}
                                <small class="text-muted"> {{ __('from 9.00 to 18.00') }} </small>
                            </h3>

                            <video class="col-12 margin-t20 margin-b20" loop autoplay controls="true" width="100%" height="100%" muted="muted" preload="true" poster="{{ URL::asset('assets/video/pool.jpg') }}">
                                <source src="https://assets.object.pscloud.io/video/pool.mov" type="video/mov"/>
                                <source src="https://assets.object.pscloud.io/video/pool.mp4" type="video/mp4" />
                                <source src="https://assets.object.pscloud.io/video/pool.ogg" type="video/oog" />
                                Your browser does not support the video tag.
                            </video>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

