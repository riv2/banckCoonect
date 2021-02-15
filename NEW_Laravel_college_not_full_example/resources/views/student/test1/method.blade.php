@extends('layouts.app')

@section('title',__('Selecting Test 1 method'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Selecting Test 1 method')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <h4>@lang('Please choose a method of testing 1 in the discipline') "{{$studentDiscipline->discipline->name}}"</h4>

                            <div>
                                <input type="button" value="@lang('Take an audience test')" class="btn btn-info" disabled title="@lang('Available on mobile devices only')">
                                &nbsp;&nbsp;
                                @if(false)
                                    @lang('or')
                                    <a role="button" class="btn btn-info" href="{{route('remoteAccessPay', ['id' => $studentDiscipline->discipline->id, 'test' => 'test1'])}}">@lang('Take the test remotely')</a>
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