@extends('layouts.app')

@section('title', __('Appeal'))

@section('content')
    <section class="content">
        <div class="container-fluid" id="study-app">
            <div class="p-3 mb-2 bg-info"><h2 class="text-white no-margin">@lang('Appeal')</h2></div>
            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <div>
                                <strong>@lang('Discipline'):</strong> {{$discipline->name}}
                            </div>
                            <div>
                                <strong>@lang('Type'):</strong> @lang('appeal_type_' . $appeal->type)
                            </div>
                            <div>
                                <strong>@lang('Test Date'):</strong> {{$quizResult->created_at->format('d.m.Y H:i')}}
                            </div>

                            <div>
                                <strong>@lang('Result'):</strong> {{$quizResult->value}} ({{$quizResult->letter}}), {{$quizResult->points}}
                            </div>

                            <div>
                                <strong>@lang('Appeal Date'):</strong> {{$appeal->created_at->format('d.m.Y H:i')}}
                            </div>

                            <div>
                                <strong>@lang('Reason'):</strong> {{$appeal->reason}}
                            </div>

                            <div>
                                <strong>@lang('Appeal Status'):</strong>
                                @if ($appeal->status == \App\Appeal::STATUS_REVIEW)
                                    <span class="label label-warning">@lang('appeal_status_'. $appeal->status)</span>
                                @elseif ($appeal->status == \App\Appeal::STATUS_APPROVED)
                                    <span class="label label-success">@lang('appeal_status_'. $appeal->status)</span>

                                    <div><strong>@lang('Decision'):</strong> @lang('appeal_resolution_action_' . $appeal->resolution_action)</div>

                                    @if ($appeal->resolution_action == \App\Appeal::RESOLUTION_ACTION_ADD_VALUE)
                                        <div><strong>@lang('Added points'):</strong> {{$appeal->added_value}}</div>
                                    @endif
                                @elseif ($appeal->status == \App\Appeal::STATUS_DECLINED)
                                    <span class="label label-danger">@lang('appeal_status_'. $appeal->status)</span>
                                @else
                                    @lang('appeal_status_'. $appeal->status)
                                @endif
                            </div>

                            <div class="margin-t15">
                                <a href="{{route('study')}}" class="btn btn-default">@lang('Back to discipline list')</a>
                            </div>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection