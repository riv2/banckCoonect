@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Speciality check list page')}}</div>

                    <div class="panel-body">
                        @if($checkEnt !== null)
                        <div class="alert @if($checkEnt) alert-success @else alert-danger @endif">
                            {{_("ENT check")}} - @if($checkEnt) {{_("Yes")}} @else {{_("No")}} @endif
                        </div>
                        @endif

                        @if($checkEnt || $checkEnt === null)
                        <div>
                            <a href="{{ route('specialityConfirm', ['id' => $specialityId]) }}" class="btn btn-primary pull-right">{{ __('Continue') }}</a>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
