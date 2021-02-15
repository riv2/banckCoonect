@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">


            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-1"></div>
                        <div class="col-md-10">

                            <div class="row banners-bottom-menu no-margin">
                                <div class="col-3 item">
                                    <a href="{{route('genWifiPage')}}">
                                        <h3 class="font-size20"><i class="fas fa-wifi text-danger"></i></h3>
                                        <p class="text-danger font-size12">Wifi</p>
                                    </a>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-1"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection