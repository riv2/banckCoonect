
@extends('promo.layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Promo page</div>

                <div class="panel-body row">

                    


                    <div class="col-md-12">
                    <blockquote>{{__("Please upload photo of your ID of both side.")}}</blockquote>

                    <form class="form-horizontal" method="POST" enctype="multipart/form-data" action="{{ route('promoIDPost') }}">
                        {{ csrf_field() }}


                        <div class="form-group{{ $errors->has('front') ? ' has-error' : '' }}">
                            <label for="front" class="col-md-4 control-label">{{__('Front side of ID')}}</label>

                            <div class="col-md-6">
                                <input id="front" type="file" class="form-control" name="front" value="{{ old('front') }}" required autofocus>

                                @if ($errors->has('front'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('front') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('back') ? ' has-error' : '' }}">
                            <label for="back" class="col-md-4 control-label">{{__('Back side of ID')}}</label>

                            <div class="col-md-6">
                                <input id="back" type="file" class="form-control" name="back" value="{{ old('back') }}" required autofocus>

                                @if ($errors->has('back'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('back') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-md-8 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{__("Send")}}
                                </button>
                            </div>
                        </div>
                    </form>

                    

                    
                    
                </div>


                    
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
