
@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Register agitator')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">


                    <form method="POST">

                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="name" class="control-label">{{__('Full name')}}</label>

                            <div class="col-md-12">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                            <label for="phone" class="control-label">{{__('Phone number')}}</label>
                            <div class="col-md-12">
                                <div class="input-group col-md-12">
                                    <div class="input-group-addon">
                                        <span class="input-group-text">+7</span>
                                    </div>
                                    <input id="phone" type="string" class="form-control" name="phone" value="{{ old('phone') }}" required autofocus minlength="7">

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-12 text-right">
                                <button type="submit" class="btn btn-info">
                                    {{__('Register')}}
                                </button>
                            </div>
                        </div>
                    </form>


                </div>
            </div>

        </div>
    </section>

@endsection
