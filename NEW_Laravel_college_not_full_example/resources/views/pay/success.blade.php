@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Payment completed successfully')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            @if($payDocument->type == \App\PayDocument::TYPE_DISCIPLINE)
                                {{ __('Payment completed successfully') }} <a href="{{route('study')}}">{{ __('Back to the list of disciplines') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_LECTURE)
                                {{ __('Payment completed successfully') }} <a href="{{route('studentShopDetails', ['id' => $payDocument->lectures->first()->course_id])}}">с</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_LECTURE_ROOM)
                                {{ __('Payment completed successfully') }} <a href="{{route('teacherScheduleList', ['id' => $payDocument->lectureRooms->first()->course_id])}}">{{ __('Back to schedule') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_REGISTRATION_FEE)
                                {{ __('Payment completed successfully') }} <a href="{{$back ?? route('userProfile')}}">{{ __('Continue') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_RETAKE_TEST)
                                {{ __('Payment completed successfully') }} <a href="{{route('studentQuiz', ['id' => $payDocument->studentDiscipline()->first()->discipline_id])}}">{{ __('Return to retake') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_RETAKE_KGE)
                                {{ __('Payment completed successfully') }} <a href="{{route('studentQuizKge')}}">{{ __('Return to retake') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_TEST)
                                Тестовый платеж успешно совершен.
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_TO_BALANCE)
                                {{ __('Payment completed successfully. Crediting funds to the balance within 5 minutes.') }} <a href="{{$back ?? route('financesPanel')}}">{{ __('Return to office') }}</a>
                            @endif

                            @if($payDocument->type == \App\PayDocument::TYPE_WIFI)
                                {{ __('Payment completed successfully') }}
                                <br><br>
                                <a class="btn btn-primary" href="{{$back ?? route('wifi')}}">{{ __('Return to wifi page') }}</a>
                            @endif

                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection
