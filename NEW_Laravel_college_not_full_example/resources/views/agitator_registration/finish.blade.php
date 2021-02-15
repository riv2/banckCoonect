@extends('layouts.app')

@section('title', __('Completion of registration'))

@section('content')

    <section class="content">
        <div class="container-fluid" id="input-bank">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Completion of registration')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            <form class="form-inline" action="{{ route('agitatorRegisterProfileFinishPost') }}" method="post">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info"> {{__('Complete registration')}} </button>
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

