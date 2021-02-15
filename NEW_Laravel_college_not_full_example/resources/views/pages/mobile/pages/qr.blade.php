@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> QR </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <button class="btn btn-info btn-lg" type="button"> {{ __('Pay') }} </button>

                </div>
            </div>

        </div>
    </section>

@endsection

