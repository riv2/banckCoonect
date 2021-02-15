@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="study-app">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __('Problem report') }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <h4>{{__("If you find a mistake and want to notify us about it, then please use this form")}}</h4>

                    {!! Form::open(array('url' => array( route('errorReportPOST') ),'class'=>'padding-15','method'=>'POST')) !!}

                    <div class="form-group row">

                        @if( !empty($profile) )
                            <input name="user_id" type="hidden" value="{{ $user_id }}" />
                            <input name="fio" type="hidden" value="{{ $profile->fio }}" />
                            <input name="phone" type="hidden" value="{{ $profile->mobile }}" />
                        @else
                            <div class="col-12"><p>{{__("Full name")}}</p></div>
                            <div class="col-12">
                                <p>
                                    <input class="form-control" name="fio" type="text" value="" required autofocus />
                                </p>
                            </div>
                            <div class="col-12"><p>{{__("Phone number")}}</p></div>
                            <div class="col-12">
                                <p>
                                    <input class="form-control" name="phone" type="text" value="" required />
                                </p>
                            </div>
                        @endif

                        <div class="col-12"><p>{{__("Specified reason")}}</p></div>
                        <div class="col-12">
                            <p>
                                <select class="form-control" name="specified_reason">
                                    <option value="" selected>...</option>
                                    <option value="Cancellation of purchase discipline"> {{ __('Cancellation of purchase discipline') }} </option>
                                </select>
                            </p>
                        </div>

                        <div class="col-12"><p>{{__("State the problem")}}</p></div>

                        <div class="col-12">
                            <p>
                                {{Form::textarea('message', '', ['class' => 'grey form-control', 'id' => 'message'])}}
                            </p>
                        </div>

                        <div class="col-12">
                            <p>
                                <button id="submit" type="submit" class="btn btn-info btn-lg"> {{__("Send")}}</button>
                            </p>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

                </div>
            </div>

        </div>
    </section>
@endsection

