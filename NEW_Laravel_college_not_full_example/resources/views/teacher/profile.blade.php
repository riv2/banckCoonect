@extends('layouts.app_old')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">{{__('Teacher profile page')}}</div>

                    <div class="panel-body">
                        <div class="row">

                            <!--<div class="col-md-12">
                                <p><a href="{{route('teacherProfileID')}}">ProfilePhotoID edit (will be deleted in product)</a></p>
                                <p><a href="{{route('teacherProfileEdit')}}">Profile edit (will be deleted in product)</a></p>
                            </div>-->

                            <div class="col-md-8 col-md-offset-2">
                                <ul class="list-group">
                                    <li class="list-group-item" style="text-align: center">
                                        <img
                                                src="{{ \App\Services\Avatar::getPublicPath($profileTeacher->photo) ?? '' }}"
                                                alt=" {{ $profileTeacher->fio ?? '' }}"
                                                style="max-width: 400px"
                                        />
                                    </li>
                                    <li class="list-group-item"> {{__('ITN')}}:  {{ $profileTeacher->iin ?? '' }} </li>
                                    <li class="list-group-item"> {{__('Full name')}}:  {{ $profileTeacher->fio ?? ''  }} </li>
                                    <li class="list-group-item"> {{__('Birth date')}}: {{ !empty($profileTeacher->bdate) ? $profileTeacher->bdate->format('Y-m-d') : '' }}  </li>

                                    <li class="list-group-item">
                                    @if($profileTeacher->doctype == \App\Teacher\ProfileTeacher::DOCTYPE_PASS)
                                         {{__('Passport number')}}
                                    @else
                                         {{__('ID number')}}
                                    @endif
                                        : {{ $profileTeacher->docnumber ?? ''  }} </li>

                                    <li class="list-group-item"> {{__('Issuing authority')}}:  {{ $profileTeacher->issuing ?? ''  }} </li>
                                    <li class="list-group-item"> {{__('Issue date')}}:  {{ !empty($profileTeacher->bdate) ? $profileTeacher->issuedate->format('Y-m-d') : '' }} </li>

                                    @if($profileTeacher->sex == \App\Teacher\ProfileTeacher::SEX_MALE)
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Male')}}</li>
                                    @else
                                        <li class="list-group-item">{{__('Sex')}}: {{__('Female')}}</li>
                                    @endif

                                    <li class="list-group-item"> {{__('Mobile phone')}}: {{ $profileTeacher->mobile ?? ''  }}</li>

                                </ul>
                            </div>

                            <div class="col-md-4 text-center">
                                <a class="btn btn-info" href="{{ route('teacherCourseEdit', ['id' => 'add']) }}">{{__('Courses')}}</a>
                                <a class="btn btn-info" href="{{ route('teacherWifiDashboard') }}">{{__('My devices')}}</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
