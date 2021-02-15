@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Email')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <form method="POST" enctype="multipart/form-data" action="{{ route( \App\Profiles::REGISTRATION_STEP_EMAIL_POST) }}">
                                {{ csrf_field() }}

                                <div class="form-group">
                                    <label for="front" class="control-label">{{__('Input email')}}</label>
                                    <input class="form-control" type="email" name="email" value="" autofocus />
                                </div>

                                <div class="form-group text-right">
                                    <button type="submit" class="btn btn-info">
                                        {{__("Send")}}
                                    </button>
                                </div>
                            </form>

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
