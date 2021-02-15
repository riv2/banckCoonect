@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{_("Documents page")}}</div>

                <div class="panel-body">
                    <blockquote>{{_("You can create a transcript file directly on our website")}}</blockquote>
                    <a href="{{ route('generateTranscriptPDF') }}" class="btn btn-info">{{_("Get a transcript")}}</a>
                    
                    @if(isset($transcripts[0]))
                        <h4>{{_("Files history")}}</h4>
                        <ul id="transcriptList" class="list-group">
                           @foreach($transcripts as $transcript)
                            <li class="list-group-item">
                                {{ $transcript->created_at->format('d.m.Y') }}
                                <a href='{{ URL::asset('transcripts/' . $transcript->filename) }}'>{{ $transcript->filename }}</a>
                            </li>
                           @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
