@extends('layouts.app_old')

@section('title', "Журнал")

@section('content')
    @include('teacher.journal.partials.table')
@endsection