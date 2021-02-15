<?php
/**
 * @var \App\StudentDiscipline[] $studentDisciplines
 *
 */

$prefix = '';

$newUserTimestamp = (new DateTimeImmutable('2019-01-01'))->getTimestamp();
?>

@if(count($studentDisciplines) > 0)
    <div class="col-12 padding-0 margin-t25" id="subjects">
        {{-- Has not confirmed Study Plan       --}}
        @if ($notConfirmedPlanDisciplines->isNotEmpty())
            <div class="panel panel-default margin-b20">
                <div class="panel-heading">
                    <h3 class="panel-title">@lang('Please confirm the individual study plan')</h3>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered table-striped">
                        <tr>
                            <th>@lang('Discipline')</th>
                            <th>@lang('Semester')</th>
                        </tr>

                        @foreach($notConfirmedPlanDisciplines as $planDiscipline)
                            <tr>
                                <td>{{$planDiscipline->discipline->name}}</td>
                                <td>{{$planDiscipline->plan_semester}}</td>
                            </tr>
                        @endforeach
                    </table>

                    <button class="btn btn-lg btn-success" v-on:click="confirmStudyPlan()" :disabled="confirming">@lang('Confirm study plan')</button>
                    <button href="{{route('studentNotConfirmStudyPlan')}}" class="btn btn-lg btn-default" :disabled="confirming" v-on:click="notConfirmStudyPlan">@lang('Not agree')</button>
                </div>
            </div>
        @endif

        {{--<div class="mb-3">  // задача https://tasks.hubstaff.com/app/organizations/16298/projects/130300/tasks/1166356
            <a href="{{ route('student.plagiarism.show') }}" class="btn btn-default col">
                {{ __('Plagiarism Checker') }}
            </a>
        </div>--}}
       
        <div class="alert alert-info">@lang('Semester'): {{$currentSemesterString}}</div>
        <div class="alert alert-info">GPA: {{$gpa}}</div>

        <div class="accordion" id="accordionExample">
            @foreach($studentDisciplines as $key => $discipline)
                @if(class_basename($discipline) == 'StudentSubmodule')
                    @include('student.study.disciplineTabSubmodule', ['submodule' => $discipline])
                @else
                    @include('student.study.disciplineTabDiscipline')
                @endif
            @endforeach
        </div>

        @if(false && Auth::user()->study_year == 1)
            <br>

            <div>
                <h4>{{__('Catalog of Elective Disciplines')}}</h4>

                @if(empty(Auth::user()->studentProfile->elective_speciality_id))
                    <form action="{{route('setElectiveSpeciality')}}" method="post">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="elective_speciality_id">{{__('Please select the specialty')}}</label>
                            <select class="form-control" name="elective_speciality_id">
                                <option></option>
                                @foreach($electiveSpecialities as $speciality)
                                    <option value="{{ $speciality->id }}">{{ $speciality->$locale }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-info btn-lg">{{__('Choose')}}</button>
                    </form>
                @else
                    <div class="accordion" id="accordionExampleelective">
                        @foreach($electiveDisciplines as $key => $discipline)
                            @if(class_basename($discipline) == 'StudentSubmodule')
                                @include('student.study.disciplineTabSubmodule', ['submodule' => $discipline, 'prefix' => 'elective'])
                            @else
                                
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endif
