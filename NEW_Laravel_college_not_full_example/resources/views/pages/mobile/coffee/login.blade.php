@extends('layouts.app')

@section('content')

<section class="content">
    <div class="container-fluid">


        <div class="card shadow-sm p-3 mb-5 bg-white rounded">
            <div class="card-body">
                {!! Form::open(array('url' => array( route('coffeeLogin') ),'class'=>'form-horizontal padding-15','method'=>'POST')) !!}

                <div class="row">
                    <div class="col-md-2"></div>
                    <label for="email" class="col-sm-6 control-label">{{__("Please enter the key")}}</label>
                </div>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-4">
                        <p>
                        {{Form::text('keynumber', '', ['class' => 'grey form-control', 'id' => 'keynumber'])}}
                        </p>

                    </div>
                    <div class="col-md-2"></div>

                </div>
                <div class="row">
                    <div class="col-md-2"></div>

                    <div class="col-md-8">
                        <p>
                            <button id="usedSubmit" type="submit" class="button btn-info">{{__("Send")}}</button>
                        </p>
                    </div>
                    <div class="col-md-2"></div>

                </div>
                {!! Form::close() !!}
            </div>
        </div>

    </div>
</section>

@endsection