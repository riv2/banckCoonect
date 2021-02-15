@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('Registration fee require')}}</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12" id="subjects">
                            <div>
                                <p>{{__('To continue you need to pay registration fee')}}: {{$feePrice}} Т</p>

                                <p>
                                    <a class="btn btn-info" href="{{ route('payRegistrationFee') . ((isset($back) && $back) ? '?back=' . $back : '')}}">{{__('Pay')}}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
