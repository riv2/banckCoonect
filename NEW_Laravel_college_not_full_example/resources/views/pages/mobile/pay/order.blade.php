@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Order information') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <form name="payform" method="post" action="/pay/test">
                        <input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}" />
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">{{__('Amount')}}*:</label>
                            <div class="col-sm-10">
                                <input type="number" name="amount" /> KZT
                            </div>
                        </div>
                        <div class="row">
                            <button type="submit">{{__('Pay')}}</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </section>

@endsection
