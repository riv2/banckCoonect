@extends('layouts.app')

@section('title', __('Test 1 result'))

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin">@lang('Test 1 result')</h2></div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    @if ($studentsDiscipline->test1_blur)
                        <div class="bg-warning margin-b15">@lang('The test was completed ahead of schedule due to the fact that you left the page for more than 5 seconds.')</div>
                    @endif
                    <div class="margin-b15">@lang('Test passed'). @lang('Your result'): {{$studentsDiscipline->test1_result}}%, {{$studentsDiscipline->test1_result_points}} @lang('points out of') {{$maxTest1Points}}.</div>

                    <div class="margin-b15">
                        @lang('Discipline'): {{ $studentsDiscipline->discipline->name ?? '' }}
                    </div>
                    <div class="margin-b15">
                        @lang('Full name'): {{ \App\Services\Auth::user()->studentProfile->fio ?? '' }}
                    </div>

                    @if(false)
                    <div class="text-center margin-b15">@lang('If you are not satisfied with the result, the test may be considered a trial')</div>

                    <a href="{{route('studentTest1Trial', ['id' => $studentsDiscipline->discipline_id])}}" class="btn btn-success">@lang('Trial')</a>
                    @endif

                    <a href="{{route('study')}}" class="btn btn-default">@lang('Back to discipline list')</a>
                </div>
            </div>
        </div>
    </section>
@endsection

