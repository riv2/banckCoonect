@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <form method="post" action="" >
                    {{ csrf_field() }}
                    <div class="form-group col-md-12">
                        <div class="col-md-5">
                            <label>{{ __('Phone for callback') }}:</label>
                        </div>
                        <div class="col-md-4">
                            @if(isset($phone) && $phone != '')
                                {{ $phone }}
                            @else
                                <input class="form-control" type="text" name="phone" required value="{{ $phone }}" />
                            @endif
                        </div>
                        @if( count(Auth::user()->helpRequests) === 0 )
                        <div class="col-md-3">
                            <button class="btn btn-primary">{{ __('Please call me')  }}</button>
                        </div>
                        @endif
                    </div>
                </form>
                @if( count(Auth::user()->helpRequests) > 0 )
                <div class="col-md-12 alert alert-success">
                    {{ __('Appeal sent. We will contact you shortly.') }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection