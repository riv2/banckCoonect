<?php
/**
 * @var App\StudentDiscipline $discipline
 * @var array $allowedGroupsToBuy
 */

$miningDisciplines = [
    '6',
    '913',
    '914',
    '915',
    '916',
    '917',
    '918',
    '920',
    '921',
    '922',
    '923',
    '924',
    '925',
    '926',
    '927',
    '928',
    '919',
    '930',
    '929',
    '1875',
    '931',
    '1876'
];
?>
@if($discipline->chooseAvailable)
    <div class="card panel-{{$discipline->color}} discipline padding-0">

        {{-- Header --}}
        <div class="card-header panel-heading padding-0" id="heading{{$prefix}}{{$key}}" @if(!$discipline->payed && $discipline->payed_credits) style="background-color: #c5e4fb;" @endif>
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$prefix}}{{$key}}" aria-expanded="true" aria-controls="collapse{{$prefix}}{{$key}}">
                    @if(!$discipline->payed && $discipline->migrated == 1)
                        >>
                    @endif

                    {{$discipline->discipline->name}}

                    @if($discipline->plan_semester && $discipline->plan_admin_confirm && $discipline->plan_student_confirm)
                        ({{$discipline->plan_semester}})
                    @endif

                    @if($discipline->iteration > 1)
                        &nbsp(@lang('iteration') {{$discipline->iteration}})
                    @endif
                </button>
            </h2>
        </div>

        {{-- Buy panel --}}
        <div id="collapse{{$prefix}}{{$key}}" class="collapse" aria-labelledby="heading{{$prefix}}{{$key}}" data-parent="#accordionExample{{$prefix}}">
            <div class="card-body">
                @if($discipline->remote_access)
                    <div class="col-md-12 margin-t10">
                        <span class="label label-info">@lang('Remote access')</span>
                    </div>
                @endif

                {{-- Migrated --}}
                @if(!$discipline->payed && $discipline->migrated == 1)
                    <blockquote>@lang('Discipline has been approved, but payment requires')</blockquote>

                    @if($buyEnabled && !$discipline->pay_processing)
                        <a href="{{route('disciplinePay', ['id' => $discipline->discipline_id])}}" class="btn btn-success margin-t5">@lang('To pay')</a>
                    @else
                        <a href="#" class="disabled btn btn-success margin-t5">@lang('To pay')</a>
                    @endif
                @else
                    {{-- Pay button --}}
                    @if($discipline->payButtonShow)
                        @if($buyEnabled && $discipline->payButtonEnabled)
                            <div class="btn-group margin-b10 margin-t15" role="group" aria-label="Button group with nested dropdown">
                                <div class="btn-group" role="group">
                                    <button id="btnGroupDrop1" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        @lang('To pay')
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        @if ($discipline->buyAvailable)
                                            <a class="dropdown-item" href="{{route('disciplinePay', ['id' => $discipline->discipline_id])}}"> @lang('Pay in full') </a>
                                            <a class="dropdown-item" href="{{route('disciplinePartialPay', ['id' => $discipline->discipline_id])}}"> @lang('Pay in part') </a>
                                        @endif

                                        @if ($discipline->remoteAccessBuyAvailable)
                                            <a class="dropdown-item" href="{{route('remoteAccessPay', ['id' => $discipline->discipline_id])}}"> @lang('Pay online access') </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <a href="#" class="btn btn-success margin-t5 disabled">@lang('To pay')</a>
                        @endif

                        @if(false && in_array($discipline->discipline_id, $miningDisciplines) && $discipline->payed == 1)
                            <a href="{{route('miningPay', ['id' => $discipline->discipline_id])}}" class="btn btn-success margin-t5">@lang('Mining')</a>
                        @endif
                    @endif

                    {{-- Paid or partial paid --}}
                    @if($discipline->syllabusButtonShow)
                        <a href="{{route('studentSyllabus', ['disciplineId' => $discipline->discipline->id])}}" class="btn btn-info margin-t5">@lang('Syllabus')
                            @if($discipline->syllabus_updated)
                                <i class="icon-circle">!</i>
                            @endif
                        </a>
                    @endif

                    {{-- Test 1 Result --}}
                    @if($discipline->test1_result !== null)
                        @if($discipline->test1_blur)
                            <div class="alert alert-warning font-size12 margin-t15 margin-b15" role="alert">
                                @lang('The test was completed ahead of schedule due to the fact that you left the page for more than 5 seconds.')
                            </div>
                        @endif

                        <div class="alert alert-success font-size12 margin-t15 margin-b15" role="alert">
                            @lang('Test 1 result'): {{$discipline->test1_result}}%&nbsp;({{$discipline->test1_result_letter}})
                        </div>
                    @endif

                    @if($discipline->discipline->disciplineSemesters()->count() > 0)
                        @foreach($discipline->discipline->getDisciplineSemestersByStudyForm(Auth::user()->studentProfile->education_study_form) as $semester)
                            <div>
                                <label for="">@lang('Semester') {{$semester->semester}}</label>
                            </div>
{{--                            @if($discipline->examButtonShowOnSemester($semester->semester))--}}
{{--                                <div class="pl-5">--}}
{{--                                    <button class="btn btn-success">@lang('Exam')</button>--}}
{{--                                </div>--}}
{{--                            @endif--}}
                        @endforeach
                    @endif

                    {{-- Test 1 --}}
                    @if($discipline->test1ButtonShow)
                        @if ($discipline->test1ButtonEnabled)
                            <a href="{{route('studentSelectTest1Method', ['id' => $discipline->discipline->id])}}" role="button" class="btn btn-success margin-t5">@lang('Test') 1</a>
                        @else
                            <a href="" role="button" class="btn btn-success margin-t5 disabled">@lang('Test') 1</a>
                        @endif
                    @endif

                    {{-- Test1 Appeal --}}
                    @if($discipline->test1AppealButtonShow)
                        <a href="{{route('studentTest1Appeal', ['id' => $discipline->discipline->id])}}" role="button" class="btn btn-warning" style="margin-top: 5px;">@lang('Appeal')</a>
                    @endif

                    {{-- SRO Results --}}
                    @if($discipline->task_result !== null)
                        <div class="alert alert-success font-size12 margin-t15 margin-b15" role="alert">
                            @lang('SRO Result'): {{$discipline->task_result}}% ({{$discipline->task_result_letter}}), {{$discipline->task_result_points}}
                        </div>
                    @endif

                    {{-- SRO --}}
                    @if($discipline->SROButtonShow)
                        @if ($discipline->SROButtonEnabled)
                            <a class="btn btn-success margin-t5" href="{{route('sroGetList', ['discipline_id' => $discipline->discipline->id])}}">@lang('SRO')</a>
                        @else
                            <a href="" role="button" class="btn btn-success margin-t5 disabled">@lang('SRO')</a>
                        @endif
                    @endif

                    {{-- Exam Results --}}
                    @if($discipline->test_result !== null)
                        @if($discipline->test_blur)
                            <div class="alert alert-warning font-size12 margin-t15 margin-b15" role="alert">
                                @lang('The test was completed ahead of schedule due to the fact that you left the page for more than 5 seconds.')
                            </div>
                        @endif

                        <div class="alert alert-success font-size12 margin-t15 margin-b15" role="alert">
                            @lang('Exam result'): {{$discipline->test_result}}%&nbsp;({{$discipline->test_result_letter}})
                        </div>
                    @endif

                    {{-- Has test result - Retake --}}
                    @if($discipline->final_result !== null)
                        <div class="alert alert-success font-size12 margin-t15 margin-b15" role="alert">
                            @lang('Discipline Grade'): {{$discipline->final_result}}% ({{$discipline->final_result_letter}}), {{$discipline->final_result_points}}
                        </div>
                        {{--                        @if(!$discipline->hasOpenDisciplinesCount() && !\App\Services\Auth::user()->keycloak && $userCreatedTimestamp > $newUserTimestamp)--}}
                        {{--                            <a href="{{route('studentQuiz', ['id' => $discipline->discipline_id])}}" class="btn btn-success disabled">{{__('Retake')}}</a>--}}
                        {{--                        @endif--}}
                    @endif

                    {{-- Exam Button --}}
<a href="{{route('studentSelectExamMethod', ['id' => $discipline->discipline->id])}}" role="button" class="btn btn-success" style="margin-top: 5px;">@lang('Exam')</a>

                    @if($discipline->discipline->disciplineSemesters()->count() === 0)
                        @if($discipline->examButtonShow)
                            @if($discipline->examButtonEnabled)
                                <a href="{{route('studentSelectExamMethod', ['id' => $discipline->discipline->id])}}" role="button" class="btn btn-success" style="margin-top: 5px;">@lang('Exam')</a>
                            @else
                                <a href="" role="button" class="btn btn-success disabled" style="margin-top: 5px;">@lang('Exam')</a>
                            @endif
                        @endif

                        {{-- Exam Appeal --}}
                        @if($discipline->examAppealButtonShow)
                            <a href="{{route('studentExamAppeal', ['id' => $discipline->discipline->id])}}" role="button" class="btn btn-warning" style="margin-top: 5px;">@lang('Appeal')</a>
                        @endif
                    @endif

                    {{-- Перезачет --}}
                    @if($discipline->payed && !$discipline->test1_result && !$discipline->final_result)
                        <a href="{{route('analogue', ['id' => $discipline->id])}}" class="btn btn-info" style="margin-top: 5px;">@lang('Repass')</a>
                    @endif

                    @if($discipline->discipline->is_practice || count($discipline->discipline->documents) > 0)
                        <a href="{{route('student.discipline.docs', ['discipline_id' => $discipline->discipline_id])}}" class="btn btn-info margin-t5" target="_blank">@lang('Documents')</a>
                    @endif
                    <a href="{{route('student.discipline.files', ['discipline_id' => $discipline->discipline_id])}}" class="btn btn-info margin-t5" target="_blank">@lang('Files')</a>


                    @if(!\App\Services\Auth::user()->keycloak)
                        @if( isset($discipline->forum_url) && Config::get('chatter.routes.home'))
                            <a href="{{route('chatter.category.show', ['slug' => $discipline->forum_url])}}" class="btn btn-info margin-t5" target="_blank">@lang('Forum')</a>
                        @endif

                        <span class="pull-right">
                            &nbsp; {{$discipline->discipline->ects}} @lang("credits")

                            @if($discipline->payed_credits || $discipline->free_credits)
                                (
                                @if($discipline->free_credits)
                                    {{$discipline->free_credits}} @lang("free")@if($discipline->payed_credits),@endif
                                @endif

                                @if($discipline->payed_credits)
                                    {{$discipline->payed_credits}} @lang("bought")
                                @endif
                                )
                            @endif
                        </span>
                    @endif

                    {{-- To cancel pay button --}}
                    @if($discipline->payed_credits)
                        @if($discipline->payCancelButtonShow)
                            <a id="discipline_cancel_button_{{$discipline->discipline_id}}" class="btn btn-default margin-t5" v-on:click="cancelPayment({{$discipline->discipline_id}})">@lang('Cancel payment')</a>
                        @endif

                        <span class="alert alert-warning" id="discipline_cancel_sent_{{$discipline->discipline_id}}" @if(!in_array($discipline->discipline_id, $cancelPayDisciplineIdList)) style="display: none;" @endif>@lang('Disciplinary cancellation request sent')</span>
                    @endif
                @endif

                @if($discipline->final_result === null)
                    {{-- Test 1 errors --}}
                    @if($discipline->test1ButtonShow)
                        @if(!$discipline->test1ButtonEnabled)
                            <div class="col-md-12 margin-t10">
                                <span>@lang('test1AvailableError', ['credits' => 1])</span>
                            </div>
                        @endif
                    @endif

                    {{-- SRO errors --}}
                    @if($discipline->SROButtonShow)
                        @if(!$discipline->SROButtonEnabled)
                            <div class="col-md-12 margin-t10">
                                <span>@lang('SROAvailableError', ['credits' => 2])</span>
                            </div>
                        @endif
                    @endif

                    {{-- Exam errors --}}
                    @if($discipline->examButtonShow)
                        @if(!$discipline->examButtonEnabled)
                            <div class="col-md-12 margin-t10">
                                <span>@lang('To access the exam, you must completely buy the discipline')</span>
                            </div>
                        @endif

                        @if(!Auth::user()->distance_learning && $discipline->test1_result === null)
                            <div class="col-md-12 margin-t10">
                                <span>@lang('To access the exam you must pass the Test 1')</span>
                            </div>
                        @endif

                        @if(!Auth::user()->distance_learning && $discipline->task_result === null)
                            <div class="col-md-12 margin-t10">
                                <span>@lang('To access the exam you must pass the SRO')</span>
                            </div>
                        @endif
                    @endif
                @endif

                @if(!$buyEnabled && $discipline->buyAvailable && !$discipline->payed && !$discipline->final_result)
                    <div class="col-12 margin-t10">
                        <p class="bg-warning padding-5">@lang('The ability to purchase disciplines is temporarily unavailable').</p>
                    </div>
                @endif

                @if($discipline->pay_processing)
                    <div class="col-12 margin-t10">
                        <p class="bg-warning padding-5">@lang('Payment is not available, because the transaction is in processing. Refresh the page in a few seconds.')</p>
                    </div>
                @endif

                {{-- Dependencies --}}
                @if(isset($discipline->discipline->depWithoutResult) and  count($discipline->discipline->depWithoutResult))
                    <div class="col-12 margin-t10">
                        <span>@lang('For this discipline, you need to complete other disciplines'):</span>
                        <ul>
                            @foreach($discipline->discipline->depWithoutResult as $depList)
                                <li>
                                    @php $depCount = (int)count($depList)-1 @endphp
                                    @foreach($depList as $i => $dep)
                                        "{{ $dep[$locale] }}" @if($depCount-- !== 0) {{ __('or') }} @endif
                                    @endforeach
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="card discipline padding-0" style="background-color: #e8e8e8;">
        {{-- Header --}}
        <div class="card-header panel-heading padding-0" id="heading{{$prefix}}{{$key}}">
            <h2 class="mb-0">
                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse{{$prefix}}{{$key}}" aria-expanded="true" aria-controls="collapse{{$prefix}}{{$key}}">
                    {{$discipline->discipline->name}}
                </button>
            </h2>
        </div>
    </div>
@endif
