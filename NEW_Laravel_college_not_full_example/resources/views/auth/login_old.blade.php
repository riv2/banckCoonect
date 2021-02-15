@extends('layouts.app_old')

@section('content')
@if(!Session::has('flash_message'))

<div class="container">

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="col-md-12 alert alert-info" style="background-color: #e3f2fd; border-color: #e3f2fd;" >
                <h4 style="font-weight: bold; line-height: 30px;" class="text-center">{{__('WELCOME TO THE EDUCATIONAL PORTAL OF THE MIRAS UNIVERSITY')}}!</h4>
                <p>{{ __('Log in to the portal with your login, either register for admission or study at educational courses') }}.</p>
            </div>
        </div>

        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('Login')}}</div>

                <div class="panel-body">
                    <br>
                    <form class="form-horizontal" method="POST" action="{{ route('teacherLogin') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">{{__('Phone or e-mail address')}}</label>

                            <div class="col-md-6">
                                <input id="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">{{__('Password')}}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>{{__('Remember Me')}}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{__('Login')}}
                                </button>

                                <a class="btn btn-primary" href="{{ route('register') }}">{{__('Register')}}</a>

                                <a class="btn btn-link" href="{{ route('paswordEmail') }}">
                                    {{__('Forgot your password?')}}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
