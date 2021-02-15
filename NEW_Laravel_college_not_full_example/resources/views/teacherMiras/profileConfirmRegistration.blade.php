@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">

                    <div class="panel-heading">{{__('Teacher panel page')}}</div>
                    <div class="panel-body">
                        {{ __('Data successfully saved and sent for processing.') }}
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
