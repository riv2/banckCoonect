@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="study-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Callback Request') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    @if( empty($profile->mobile) )
                        <h4>{{__("If you need advice, leave your phone number here and we will contact you")}}</h4>
                    @else
                        <h4>{{__("If you need advice, click on the \"call back\" button and we will call you back to the number")}} {{$profile->mobile}} </h4>
                    @endif

                    {!! Form::open(array('url' => array( route('callBack') ),'class'=>'padding-15','method'=>'POST')) !!}

                    <div class="form-group row">

                        @if( empty($profile->mobile) )
                            <label for="number" class="col-sm-2 control-label">{{__("Phone number")}}</label>
                            {{Form::text('number', '', ['class' => 'grey form-control', 'id' => 'number'])}}
                        @else
                            {{Form::hidden('number', $profile->mobile)}}
                        @endif

                        <div class="col-12 text-right">
                            <button id="usedSubmit" type="submit" class="btn btn-info mt-2">{{__("Call back")}}</button>
                        </div>
                    </div>

                    {!! Form::close() !!}

                </div>
            </div>

        </div>
    </section>
@endsection
