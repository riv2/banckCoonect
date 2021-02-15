@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="study-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Shop')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    @yield('shop-content')

                </div>
            </div>

        </div>
    </section>

@endsection
