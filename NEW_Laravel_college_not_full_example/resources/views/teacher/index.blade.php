@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{ __('Profile') }}</div>

                    <div class="panel-body">
                        <div class="row">

                            <div class="col-md-12">
                                <p><a href="">ProfilePhotoID789 edit (will be deleted in product)</a></p>
                                <p><a href="">Profile edit (will be deleted in product)</a></p>

                            </div>

                            <div class="col-md-8 col-md-offset-2">
                                <ul class="list-group">
                                    12
                                    <li class="list-group-item"> {{__('Photo')}}: </li>
                                    <li class="list-group-item"> {{__('ITN')}}: </li>
                                    <li class="list-group-item"> {{__('Full name')}}: </li>
                                    <li class="list-group-item"> {{__('Birth date')}}: </li>

                                    {{--@if($profile->pass == 1)--}}
                                    <li class="list-group-item"> {{__('Passport number')}} :  </li>
                                    {{--@else--}}
                                    <li class="list-group-item"> {{__('ID number')}} : </li>
                                    {{--@endif--}}

                                    <li class="list-group-item"> {{__('Document number')}}: </li>
                                    <li class="list-group-item"> {{__('Issuing authority')}}: </li>
                                    <li class="list-group-item"> {{__('Issue date')}}: </li>

                                    {{--                                    @if($profile->sex == 1)--}}
                                    <li class="list-group-item">{{__('Sex')}}: {{__('Male')}}</li>
                                    {{--@else--}}
                                    <li class="list-group-item">{{__('Sex')}}: {{__('Female')}}</li>
                                    {{--@endif--}}

                                    <li class="list-group-item"> {{__('Mobile phone')}}: </li>

                                </ul>
                            </div>

                            {{--                            @if($user->usertype == 'Teacher')--}}
                            <div class="col-md-4 text-center">
                                <a class="btn btn-info" href="">Add Teacher Info</a>
                            </div>
                            {{--@else--}}
                            <div class="col-md-4 text-center">
                                <a class="btn btn-info" href="">bcAplication</a>
                            </div>
                            <div class="col-md-4 text-center">
                                <a class="btn btn-info" href="">mgAplication</a>
                            </div>
                            {{--@endif--}}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
