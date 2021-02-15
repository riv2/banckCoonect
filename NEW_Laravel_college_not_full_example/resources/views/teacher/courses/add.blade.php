@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('Profile') }}</div>

                    <div class="panel-body">
                        <blockquote>{{__("Add your new course")}}</blockquote>

                        <form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
                            {{ csrf_field() }}

                            <!-- COURSE IMAGE -->
                            <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                                <label for="image" class="col-md-4 control-label">{{__('Image Course')}}</label>

                                <div class="col-md-6">
                                    <input id="image" type="file" class="form-control" name="image" value="{{ old('image') }}" required autofocus maxlength="12">

                                    @if ($errors->has('image'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('image') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!-- COURSE TITLE -->
                            <div class="form-group{{ $errors->has('iin') ? ' has-error' : '' }}">
                                <label for="iin" class="col-md-4 control-label">{{__('COURSE_TITLE')}}</label>

                                <div class="col-md-6">
                                    <input id="title" type="text" class="form-control" name="title" value="{{ old('title') }}" required autofocus maxlength="12">

                                    @if ($errors->has('title'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <!-- COURSE DESCRIPTION -->
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="description" class="col-md-4 control-label">{{__('COURSE_DESCRIPTION')}}</label>

                                <div class="col-md-6">
                                    <textarea id="description" class="form-control" name="description" value="{{ old('description') }}" required autofocus></textarea>

                                    @if ($errors->has('description'))
                                        <span class="help-block">
                                    <strong>{{ $errors->first('description') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>

                            <!-- COURSE PRICE -->
                            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                                <label for="iin" class="col-md-4 control-label">{{__('COURSE_PRICE')}}</label>

                                <div class="col-md-6">
                                    <input id="price" type="number" class="form-control" name="price" value="{{ old('price') }}" required autofocus maxlength="12"></input>

                                    @if ($errors->has('price'))
                                        <span class="help-block">
                                <strong>{{ $errors->first('price') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>

                            <!-- COURSE START AT -->
                            <div class="form-group{{ $errors->has('start_at') ? ' has-error' : '' }}">
                            <label for="start_at" class="col-md-4 control-label">{{__('COURSE_START_AT')}}</label>

                            <div class="col-md-6">
                                <input id="start_at" type="date" class="form-control" name="start_at" value="{{ old('start_at') }}" required autofocus maxlength="12"></input>
                                @if ($errors->has('start_at'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('start_at') }}</strong>
                                    </span>
                                @endif
                            </div>
                            </div>

                            <!-- COURSE END AT -->
                            <div class="form-group{{ $errors->has('end_at') ? ' has-error' : '' }}">
                                <label for="start_at" class="col-md-4 control-label">{{__('COURSE_START_AT')}}</label>

                                <div class="col-md-6">
                                    <input id="end_at" type="date" class="form-control" name="end_at" value="{{ old('start_at') }}" required autofocus maxlength="12"></input>
                                    @if ($errors->has('end_at'))
                                        <span class="help-block">
                                 <strong>{{ $errors->first('end_at') }}</strong>
                                </span>
                                    @endif
                                </div>
                            </div>

                            <!-- COURSE DURATION ( 100 hours )-->
                            <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }}">
                                <label for="duration" class="col-md-4 control-label">{{__('COURSE_DURATION')}}</label>

                                <div class="col-md-6">
                                    <input id="duration" type="number" class="form-control" name="duration" value="{{ old('duration') }}" required autofocus maxlength="12"></input>
                                    @if ($errors->has('duration'))
                                        <span class="help-block">
                            <strong>{{ $errors->first('duration') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>

                            <!-- COURSE PERIOD_NUMBER ( 1/2/3.. 7/...  times )-->
                            <div class="form-group{{ $errors->has('period_number') ? ' has-error' : '' }}">
                                <label for="start_at" class="col-md-4 control-label">{{__('COURSE_PERIOD_NUMBER')}}</label>

                                <div class="col-md-6">
                                    <input id="period_number" type="number" class="form-control" name="period_number" value="{{ old('start_at') }}" required autofocus maxlength="12"></input>
                                    @if ($errors->has('period_number'))
                                        <span class="help-block">
                             <strong>{{ $errors->first('period_number') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>

                            <!-- COURSE PERIOD_INTERVAL (year/week/day/) -->
                            <div class="form-group{{ $errors->has('period_interval') ? ' has-error' : '' }}">
                                <label for="period_interval" class="col-md-4 control-label">{{__('COURSE_PERIOD_INTERVAL')}}</label>

                                <div class="col-md-6">
                                    <select name="period_interval" id="period_interval">
                                        <option value="day">{{ __('DAY') }}</option>
                                        <option value="week">{{ __('WEEK') }}</option>
                                        <option value="month">{{ __('MONTH') }}</option>
                                        <option value="year">{{ __('YEAR') }}</option>
                                    </select>
                                    @if ($errors->has('period_interval'))
                                        <span class="help-block">
                            <strong>{{ $errors->first('period_interval') }}</strong>
                            </span>
                                    @endif
                                </div>
                            </div>




                                <div class="form-group">
                                <div class="col-md-8 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{__("Add course")}}
                                    </button>
                                </div>
                            </div>
                        </form>





                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
