@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Retake test pay page')}}</div>

                    <div class="panel-body">
                        <p>
                            {{__('You pay retake comprehensive state exam')}}. {{__('It worth')}} {{$creditPrice}} T
                        </p>

                        <a href="{{route('studentPayRetakeKge')}}" class="btn btn-info">{{__('Pay')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection