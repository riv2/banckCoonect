@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Payment error')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            @if($payDocument->type == \App\PayDocument::TYPE_DISCIPLINE || $payDocument->type == \App\PayDocument::TYPE_RETAKE_TEST)
                                {{ __('An error occurred while paying') }} <a href="{{route('study')}}">{{ __('Back to the list of disciplines') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_LECTURE)
                                {{ __('An error occurred while paying') }} <a href="{{route('studentShopDetails', ['id' => $payDocument->lectures->first()->course_id])}}">{{ __('Back to the list of disciplines') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_LECTURE_ROOM)
                                {{ __('An error occurred while paying') }} <a href="{{route('teacherScheduleList', ['id' => $payDocument->lectureRooms->first()->course_id])}}">{{ __('Back to schedule') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_REGISTRATION_FEE)
                                {{ __('An error occurred while paying') }} <a href="{{$back ?? route('userProfile')}}">{{ __('Continue') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_TEST)
                                При тестовой оплате возникла ошибка.
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_TO_BALANCE)
                                {{ __('An error occurred while paying') }}. <a href="{{$back ?? route('financesPanel')}}">{{ __('Return to office') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_WIFI)
                                {{ __('An error occurred while paying') }}
                                <br><br>
                                <a href="{{route('wifi')}}" class="btn btn-primary">{{ __('Return to wifi page') }}</a>
                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
