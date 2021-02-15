@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('Import information') }}</div>

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p class="alert alert-success">{{ __('Your profile data has been successfully transferred from the shared database') }}.</p>
                            </div>
                            <div class="col-md-8 col-md-offset-2">
                                {{ __('Your profile information') }}:
                            </div>
                            <div class="col-md-8 col-md-offset-2">

                                <ul class="list-group">
                                    <li class="list-group-item"> {{__('IIN')}}: {{$profile->iin}}</li>
                                    <li class="list-group-item"> {{__('Full name')}}: {{$profile->fio}}</li>
                                    <li class="list-group-item"> {{__('Birth date')}}: {{$profile->bdate->format('d.m.Y')}}</li>

                                    @if($profile->pass == true)
                                        <li class="list-group-item"> {{__('Document type')}} : {{ __('Passport') }} </li>
                                    @else
                                        <li class="list-group-item"> {{__('Document type')}} : {{ __('ID Card') }} </li>
                                    @endif

                                    <li class="list-group-item"> {{__('Document number')}}: {{$profile->docnumber}}</li>
                                    <li class="list-group-item"> {{__('Issuing authority')}}: {{$profile->issuing}}</li>
                                    <li class="list-group-item"> {{__('Issue date')}}: {{$profile->issuedate->format('d.m.Y')}}</li>

                                    @if($profile->sex == 1)
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Male')}}</li>
                                    @else
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Female')}}</li>
                                    @endif

                                    <li class="list-group-item"> {{__('Mobile phone')}}: {{$profile->mobile}}</li>

                                </ul>
                            </div>
                            <div class="col-md-12">
                                <p>{{ __('Now you can go') }}&nbsp;
                                    <a class="btn btn-primary" href="{{ route('study') }}">{{ __('To the list of disciplines') }}</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
