@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="user_panel">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Analogue page for discipline')}} {{$discipline->name}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <h4>{{__('Please send us your certificate for this discipline for approval')}}.</h4>

                    <form method="POST" enctype="multipart/form-data" action="{{ route('analoguePost', ['id'=>$discipline->discipline_id]) }}">

                        {{ csrf_field() }}

                        <div class="field_all form-group{{ $errors->has('certificate') ? ' has-error' : '' }}">
                            <label for="certificate" class="control-label"> </label>

                            <div class="col-12">
                                <input id="certificate" type="file" class="form-control" name="certificate" value="{{ old('certificate') }}" autofocus>

                                @if ($errors->has('certificate'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('certificate') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="field_all form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
                            <label for="notes" class="control-label">{{__('Notes')}}</label>

                            <div class="col-12">
                                <textarea id="notes" class="form-control" name="notes"  autofocus>{{ old('notes') }}</textarea>

                                @if ($errors->has('notes'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('notes') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-info btn-lg">
                                    {{__("Send")}}
                                </button>
                            </div>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </section>

@endsection

