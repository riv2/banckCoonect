@extends('layouts.app')

@section('title', __('Appeal Exam'))

@section('content')
    <div class="content-wrapper height-auto">
        <div class="row no-margin">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="alert margin-20 alert-warning">
                    @lang('In case of an unreasonable complaint, payment will be charged in the amount of 1000 tenge.')
                </div>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Appeal Exam')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div>
                                <strong>@lang('Discipline'):</strong> {{$discipline->name}}
                            </div>
                            <div>
                                <strong>@lang('Exam Date'):</strong> {{$quizResult->created_at->format('d.m.Y H:i')}}
                            </div>

                            <div>
                                <strong>@lang('Result'):</strong> {{$quizResult->value}} ({{$quizResult->letter}}), {{$quizResult->points}}
                            </div>

                            <form method="post" action="{{route('studentExamAppeal', ['disciplineId' => $discipline->id])}}" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <input type="hidden" name="quiz_result_id" value="{{$quizResult->id}}">

                                <div class="form-group">
                                    <label for="reason">@lang('Reason') *</label>
                                    <textarea name="reason" maxlength="1000" class="form-control" required id="reason"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="file">@lang('Attach file')</label>
                                    <input type="file" id="file" accept=".jpg,.jpeg,.png,.gif" name="file" class="form-control" >
                                </div>

                                <input class="btn btn-info" type="submit" value="@lang('File an appeal')" />
                            </form>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection