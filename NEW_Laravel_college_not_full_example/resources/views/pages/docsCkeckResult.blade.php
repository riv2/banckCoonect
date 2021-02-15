@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{_("Documents page")}}</div>

                <div class="panel-body">
                    
                    <p>{{_("File name")}}: <b>{{ $enquire->filename }}</b></p>
                    <p>{{_("The file has been generated")}}: <b>{{ $enquire->created_at->format('d.m.Y') }}</b></p>
                    <p>{{_("You can download this document for comparison")}}:
                        <a href='{{ URL::asset($folder. $enquire->filename) }}'>{{ $enquire->filename }}</a>
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
