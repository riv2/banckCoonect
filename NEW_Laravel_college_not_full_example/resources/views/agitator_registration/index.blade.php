@extends('layouts.app')

@section('title', __('Registration of agitator'))

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Registration of agitator')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <div class="alert alert-warning" role="alert">
                                @lang('Any MAC APP user can become an agitator and get his reward in the amount of 10 000 tenge for each applicant who indicated Your phone number when applying for admission. To become an agitator, read the contract and fill in your personal data.')
                            </div>

                            <a class="btn btn-info" href="{{ route('agitatorRegisterProfileID') }}"> @lang('Registration') </a>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection

