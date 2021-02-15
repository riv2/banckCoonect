@extends('layouts.app')

@section('title', __('Files'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">@lang('Files')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-6 d-flex justify-content-center">
                            <div class="w-100">
                                <div class="alert alert-info">
                                    <ul>
                                        <li>{{ __('Upload files in: doc/docx/pdf/xls/xlsx/ppt/pptx.') }}</li>
                                        <li>{{ __('You have 5 mins to delete the file uploaded by error.') }}</li>
                                    </ul>
                                </div>
                                <ul class="list-group">
                                    @foreach($files as $file)
                                            <li class="list-group-item ">
                                                <div class="d-flex justify-content-center justify-content-lg-between align-content-center">
                                                    <div class="align-self-center jus">
                                                        @if($file->type == 'link')
                                                            <a target="_blank" href="{{ $file->link }}">{{ $file->link }}</a>
                                                        @endif
                                                        @if($file->type == 'file')
                                                            {{ $file->original_name }}
                                                        @endif
                                                    </div>

                                                    <div class="d-none d-lg-block">
                                                        @if($file->type == 'file')
                                                            <a href="{{ $file->getUrlToDownload() }}" class="btn btn-info">@lang('Download')</a>
                                                        @endif
                                                        @if($file->seconds_of_create < 300)
                                                        <a href="{{ route('student.remove.student_discipline.file', [
                                                            'discipline_id' => $discipline->id,
                                                            'file_id' => $file->id
                                                        ]) }}" class="btn btn-danger remove-button-{{$file->id}}">
                                                            @lang('Remove')
                                                            <span></span>
                                                        </a>
                                                        @endif
                                                    </div>

                                                </div>

                                                <div class="d-lg-none margin-t10">
                                                    @if($file->type == 'file')
                                                    <a href="{{ $file->getUrlToDownload() }}" class="btn btn-info w-100">@lang('Download')</a>
                                                    @endif
                                                    @if($file->seconds_of_create < 300)

                                                        <a href="{{ route('student.remove.student_discipline.file', [
                                                            'discipline_id' => $discipline->id,
                                                            'file_id' => $file->id
                                                        ]) }}" class="btn btn-danger w-100 remove-button-{{$file->id}}" style="margin-top: 10px;">
                                                            @lang('Remove')
                                                            <span></span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </li>
                                    @endforeach
                                </ul>

                                {!! Form::open(['route' => ['student.upload.file', 'discipline_id' => $discipline->id],
                                                                'files' => true,
                                                                'class' => 'd-flex align-content-center',
                                                                'method' => 'post',
                                                                'enctype' => 'multipart/form-data'
                                                                ]) !!}
                                <div class="media-body media-middle padding-l0">
                                    <input type="file" name="document"  onchange="actionForm()" class="filestyle" id="filestyle-0" tabindex="-1" style="position: absolute; clip: rect(0px, 0px, 0px, 0px);">

                                    <div class="bootstrap-filestyle d-inline input-group">
                                        <span class="group-span-filestyle button-block" tabindex="0">
                                            <label for="filestyle-0" class="btn col-12 col-lg-4 btn-info margin-t10">@lang('Add file')</label>
                                        </span>
                                        <span class="group-span-filestyle button-block" tabindex="0">
                                            <label for="filestyle-1" class="btn col-12 col-lg-4 btn-info margin-t10" onclick="showLinkForm()">@lang('Add link')</label>
                                        </span>
                                    </div>

                                    <div id="add-link-form" style="display: none; margin-top: 10px;">

                                        {{ __('Link to resource') }}: <input type="text" name="link" id="form-link" class="form-control" placeholder="http://..." />

                                        <span class="group-span-filestyle " tabindex="0">
                                            <label for="filestyle-3" class="btn col-12 col-lg-4 btn-info margin-t10" onclick="actionForm()">@lang('Add')</label>
                                        </span>
                                        <span class="group-span-filestyle " tabindex="0">
                                            <label for="filestyle-4" class="btn col-12 col-lg-4 btn-info margin-t10" onclick="cancelLinkForm()">@lang('Cancel')</label>
                                        </span>
                                    </div>

                                    <small class="text-muted bold"></small>

                                    <input type="submit" id="upload-button" style="display: none;" />
                                </div>
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script type="text/javascript">

        var timers = [];

        function actionForm() {
            $('#upload-button').click();
        }

        function showLinkForm() {
            $('.button-block').hide();
            $('#add-link-form').css('display', 'block');
            $('#form-link').attr('required', 'true');
        }

        function cancelLinkForm() {
            $('.button-block').show();
            $('#add-link-form').css('display', 'none');
            $('#form-link').removeAttr('required');
        }

        function getTimeBySeconds(seconds) {
            var secNum = seconds;
            var hours   = Math.floor(secNum / 3600);
            var minutes = Math.floor((secNum - (hours * 3600)) / 60);
            var seconds = secNum - (hours * 3600) - (minutes * 60);

            minutes = minutes < 10 ? '0' + minutes.toString() : minutes.toString();
            seconds = seconds < 10 ? '0' + seconds.toString() : seconds.toString();

            return minutes + ':' + seconds;
        }

        @foreach($files as $file)

            var time{{ $file->id }} = 300 - {{$file->seconds_of_create ?? 0}};
            $('.remove-button-' + {{$file->id}} + ' span').html( '(' + getTimeBySeconds(time{{ $file->id }}) + ')');
            var timer{{ $file->id }} = setInterval(function(){

                $('.remove-button-' + {{$file->id}} + ' span').html( '(' + getTimeBySeconds(time{{ $file->id }}) + ')');
                time{{ $file->id }}--;

                if(time{{ $file->id }} <= 0)
                {
                    $('.remove-button-' + {{$file->id}}).remove();
                }
            }, 1000);

        @endforeach

    </script>
@endsection