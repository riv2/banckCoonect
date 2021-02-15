@extends('layouts.app')

@section('title', __('Selecting exam method'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Selecting exam method')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <h4>@lang('Please choose a method of exam in the discipline') "{{$SD->discipline->name}}"
                               <p>
                                   @lang("If you click 'Take the test remotely' button then we will use webcam when you pass an exam online. Please allow access to it.")
                               </p>
                            </h4>
                            <div>
                                <input type="button" value="@lang('Take an audience test')" class="btn btn-info" disabled title="@lang('Available on mobile devices only')">
                                @if(false)
                                @lang('or')
                                <a role="button" class="btn btn-info" href="{{route('remoteAccessPay', ['id' => $SD->discipline->id, 'test' => 'exam'])}}">@lang('Take the test remotely')</a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection