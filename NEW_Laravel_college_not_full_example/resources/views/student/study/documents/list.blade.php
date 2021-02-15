@extends('layouts.app')

@section('title', __('Documents'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">@lang('Documents')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6 d-flex justify-content-center">
                            <div class="w-100">
                                <div>
                                    <h4>@lang('Not filled documents')</h4>
                                </div>
                                <ul class="list-group">
                                    @foreach($documentsNotFilled as $document)
                                            <li class="list-group-item padding-l0">
                                                <div class="d-flex justify-content-center justify-content-lg-between align-content-center">
                                                    <div class="align-self-center">
                                                        {{$document->original_name}}
                                                    </div>
                                                </div>
                                                {!! Form::open(['route' => ['student.upload.document', 'document_id' => $document->id , 'discipline_id' => $document->discipline_id],
                                                                'files' => true,
                                                                'class' => 'd-flex align-content-center',
                                                                'method' => 'post'
                                                                ]) !!}
                                                    <div class="media-body media-middle padding-l0">
                                                        <a href="{{$document->getPublicUrl()}}" target="_blank" class="btn btn-info d-block d-lg-inline-block">@lang('Download')</a>
                                                        <input type="file" name="document" class="filestyle" id="filestyle-0" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);"><div class="bootstrap-filestyle d-inline input-group"><span class="group-span-filestyle " tabindex="0"><label for="filestyle-0" class="btn col-12 col-lg-4 btn-info margin-t10">@lang('Attach file')</label></span></div>
                                                        <small class="text-muted bold"></small>
                                                        <input type="submit" class="btn btn-success col-12 col-lg-3" value="@lang('Upload')">
                                                        <button class="btn btn-primary col-12 col-lg-2" type="button" data-toggle="collapse" data-target="#multiCollapseExample{{$document->id}}" aria-expanded="false" aria-controls="multiCollapseExample{{$document->id}}">@lang('Description')</button>
                                                    </div>
                                                {!! Form::close() !!}
                                                <div class="collapse multi-collapse" id="multiCollapseExample{{$document->id}}">
                                                    <div class="card card-body">
                                                       {{$document->description}}
                                                    </div>
                                                </div>
                                            </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6 d-flex justify-content-center">
                            <div class="w-100">
                                <div>
                                    <h4>@lang('Filled documents')</h4>
                                </div>
                                <ul class="list-group">
                                    @foreach($documentsFilled as $document)
                                            <li class="list-group-item ">
                                                <div class="d-flex justify-content-center justify-content-lg-between align-content-center">
                                                    <div class="align-self-center jus">
                                                        {{$document->original_name}}
                                                    </div>
                                                    <div class="d-none d-lg-block">
                                                        <a href="{{ $document->getUrlToDownload() }}" class="btn btn-info">@lang('Download')</a>

                                                        <a href="{{ route('student.remove.file', [
                                                            'discipline_id' => $document->discipline_id,
                                                            'file_id' => $document->id
                                                        ]) }}" class="btn btn-danger">
                                                            @lang('Remove')
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="d-lg-none margin-t10">
                                                    <a href="{{$document->getUrlToDownload()}}" class="btn btn-info w-100">@lang('Download')</a>

                                                    <a href="{{ route('student.remove.file', [
                                                            'discipline_id' => $document->discipline_id,
                                                            'file_id' => $document->id
                                                        ]) }}" class="btn btn-danger w-100 mt-1">
                                                        @lang('Remove')
                                                    </a>
                                                </div>
                                            </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
