@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('QR')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <button class="btn btn-info" type="button"> {{ __('Pay') }} </button>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection