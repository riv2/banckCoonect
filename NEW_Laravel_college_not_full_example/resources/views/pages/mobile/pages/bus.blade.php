@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info">
                <h2 class="text-white no-margin">{{ __('Bus') }}</h2>
            </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        @if( app()->getLocale() == \App\Language::LANGUAGE_RU )
                            <div class="col-12">
                                <img src="{{ URL::asset('lte_assets/img/bus_schedule_mon_ru.png') }}" class="img-fluid rounded-lg" alt="" />
                            </div>
                            <div class="clearfix margin5 margin-t10"></div>
                            <div class="col-12">
                                <img src="{{ URL::asset('lte_assets/img/bus_schedule_nig_ru.png') }}" class="img-fluid rounded-lg" alt="" />
                            </div>
                        @else
                            <div class="col-12">
                                <img src="{{ URL::asset('lte_assets/img/bus_schedule_mon_kz.png') }}" class="img-fluid rounded-lg" alt="" />
                            </div>
                            <div class="clearfix margin5 margin-t10"></div>
                            <div class="col-12">
                                <img src="{{ URL::asset('lte_assets/img/bus_schedule_nig_kz.png') }}" class="img-fluid rounded-lg" alt="" />
                            </div>
                        @endif

                        <div class="col-12">
                            <video class="margin-t20 margin-b20" loop autoplay playsinline controls="true" width="100%" height="100%" muted="muted" preload="true" poster="{{ URL::asset('assets/video/miras_bus.jpg') }}">
                                <source src="https://assets.object.pscloud.io/video/miras_bus.mov" type="video/mov"/>
                                <source src="https://assets.object.pscloud.io/video/miras_bus.mp4" type="video/mp4" />
                                <source src="https://assets.object.pscloud.io/video/miras_bus.ogg" type="video/oog" />
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

