<?php
        $dayRatingByWeek = [];
        $dayCountByWeek = [];

        foreach($discipline->studentDisciplineDayLimits as $ratingDay)
        {
            if( $ratingDay->day_num / 5 - floor($ratingDay->day_num / 5) == 0)
            {
                $index = floor($ratingDay->day_num / 5);
            }
            else
            {
                $index = floor($ratingDay->day_num / 5) + 1;
            }
            $ratingDay->dayOffset = ($ratingDay->day_num / 5 - floor($ratingDay->day_num / 5) == 0) ? 5 : $ratingDay->day_num - floor($ratingDay->day_num / 5) * 5;
            $dayCountByWeek[$index][] = $ratingDay;
        }
?>

@extends('layouts.app_old')

@section('title', "Студенты группы \"$group->name\", дисциплина \"$discipline->name\"")

@section('content')
{{--    <div class="container">--}}
        <div class="row" id="main" v-cloak>
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('Students of group') "<strong>{{$group->name}}</strong>", дисциплина "<strong>{{$discipline->name}}</strong>" @if($discipline->is_practice)(практика)@endif</div>

{{--                    <div class="well">--}}
{{--                        <strong>@lang('Grade Dates')</strong>:--}}
{{--                        <span class="@if($isManualExamTime) text-success @else text-danger @endif">--}}
{{--                            @if($discipline->is_practice)--}}

{{--                            @else--}}
{{--                                @lang('from') @if(!empty($teacherGroup->date_from)) {{$teacherGroup->date_from->format('d.m.Y')}} @else н/д @endif @lang('to') @if(!empty($teacherGroup->date_to)){{$teacherGroup->date_to->format('d.m.Y')}} @else н/д @endif--}}
{{--                            @endif--}}
{{--                        </span>--}}
{{--                    </div>--}}

                    <div class="panel-body">


                        @if($discipline->disciplineSemesters->count() === 0)
                        <form action="{{route('teacherGroupSave', ['group_id' => $group->id, 'discipline_id' => $discipline->id])}}" method="post">
                            {{csrf_field()}}
                            <div style="height: 85vh;">
                            <table id="data-table"
                                   class="table table-striped table-bordered table-hover dt-responsive">
                                <thead>
                                <tr>
                                    <th rowspan="2" style="z-index:1">#</th>
                                    <th rowspan="2" style="z-index:1">ФИО</th>
                                    @if(!$discipline->is_practice)
                                        <th rowspan="2"></th>
                                    @endif

                                    @for($i=1; $i<=6; $i++)
                                        @if(isset($dayCountByWeek[$i]))
                                            <th colspan="{{ count($dayCountByWeek[$i])+1 }}">Неделя {{ $i }}</th>
                                        @else
                                            <th>Неделя {{ $i }}</th>
                                        @endif
                                    @endfor
                                    <th rowspan="2">Р1 (T1)</th>
                                    @for($i=8; $i<=20; $i++)
                                        @if(isset($dayCountByWeek[$i]))
                                            <th colspan="{{ count($dayCountByWeek[$i])+1 }}">Неделя {{ $i }}</th>
                                        @else
                                            <th>Неделя {{ $i }}</th>
                                        @endif
                                    @endfor
                                    <th rowspan="2">Р2 (СРО)</th>
                                    <th rowspan="2">Экзамен / Практика</th>
                                    <th rowspan="2">Финальная оценка</th>
                                    @if($discipline->is_practice)
                                        <th rowspan="2">{{ __('Files (practice)') }}</th>
                                    @endif

                                </tr>
                                <tr>

                                    @for($i=1; $i<=6; $i++)
                                        @if(isset($dayCountByWeek[$i]))
                                        @foreach($dayCountByWeek[$i] as $k => $ratingDay)

                                            <th>занятие {{ $ratingDay->dayOffset }}</th>

                                        @endforeach
                                        @endif
                                        <th>СРО</th>
                                    @endfor

                                    @for($i=8; $i<=20; $i++)
                                        @if(isset($dayCountByWeek[$i]))
                                        @foreach($dayCountByWeek[$i] as $k => $ratingDay)

                                            <th>занятие {{ $ratingDay->dayOffset }}</th>

                                        @endforeach
                                        @endif
                                        <th>СРО</th>
                                    @endfor

                                </tr>
                                </thead>
                                <tbody>
                                @foreach($studentsProfiles as $i => $studentProfile)
                                    <?php
                                        $studentDays = $studentProfile->getDisciplineDays($discipline->id, Auth::id());
                                    ?>
                                    <tr>
                                        <td>{{$i + 1}}</td>
                                        <td>
                                            {{$studentProfile->fio}} {{$studentProfile->user_id}}
                                        </td>
                                        @if(!$discipline->is_practice)
                                        <td>

                                                <a v-on:click="showFiles({{ $studentProfile->user_id }})" style="cursor: pointer;" v-bind:class="{'clip-file-disable': !studenFilesCount({{ $studentProfile->user_id }})}">

                                                    <i v-if="newStudenFilesCount({{$studentProfile->user_id}}) > 0" class="fa fa-paperclip badge" style="font-size: 1.2em; background-color: #30638e;">
                                                        + <span v-html="newStudenFilesCount({{$studentProfile->user_id}})"></span>
                                                    </i>

                                                    <i v-if="newStudenFilesCount({{$studentProfile->user_id}}) == 0" class="fa fa-paperclip" style="font-size: 1.2em"></i>

                                                </a>

                                        </td>
                                        @endif
                                        @if(empty($studentProfile->studentDiscipline))
                                            <td colspan="6">
                                                <p class="text-danger">Ошибка. У студента отсутствует данная дисциплина.</p>
                                            </td>
                                        @else

                                            @for($i = 1; $i <= 6; $i++)
                                                @if(isset($dayCountByWeek[$i]))
                                                @foreach($dayCountByWeek[$i] as $ratingDay)
                                                    <td>
                                                        <div>
                                                            <label for="">Оценка</label>
                                                            <input
                                                                class="form-control"
                                                                style="display: inline;width: 150px;"
                                                                type="number"
                                                                min="0"
                                                                max="100"
                                                                name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][rating]"
                                                                maxlength="3"
                                                                value="{{ $studentDays->where('day_num', $ratingDay->day_num)->first()->rating ?? '' }}">
                                                        </div>
                                                        <div>
                                                            <label for="">Дата</label>
                                                            <input
                                                                type="date"
                                                                class="form-control"
                                                                style="display: inline;width: 150px;"
                                                                min="0"
                                                                max="100"
                                                                name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][date]"
                                                                maxlength="3"
                                                                value="{{ $studentDays->where('day_num', $ratingDay->day_num)->first()->date ?? '' }}">
                                                        </div>
                                                    </td>
                                                @endforeach
                                                @endif

                                                <td>
                                                    <?php $weekField = "week{$i}_result";?>
                                                    <label for="">Оценка</label>
                                                    <input
                                                        class="form-control"
                                                        style="display: inline;width: 70px;"
                                                        type="number"
                                                        min="0"
                                                        max="100"
                                                        name="data[{{$studentProfile->user_id}}][week{{$i}}_result]"
                                                        value="{{$studentProfile->studentDiscipline->$weekField}}"
                                                        maxlength="3">
                                                </td>
                                            @endfor

                                            <td>
                                                {{--                                                <input class="form-control" style="display: inline;width: 70px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][test1_result]" value="{{$studentProfile->studentDiscipline->test1_result}}" maxlength="3">--}}

                                                {{--                                                @if($studentProfile->user_id==8251) {{dd($studentProfile->studentDiscipline)}} @endif--}}

                                                @if($studentProfile->studentDiscipline->test1_result !== null)
                                                    {{$studentProfile->studentDiscipline->test1_result}}
                                                    ({{$studentProfile->studentDiscipline->test1_result_letter}})
                                                @endif
                                            </td>


                                            @for($i = 8; $i <= 20; $i++)
                                                @if(isset($dayCountByWeek[$i]))
                                                @foreach($dayCountByWeek[$i] as $ratingDay)
                                                    <td>
                                                        <div>
                                                            <label for="">Оценка</label>
                                                            <input
                                                                    class="form-control"
                                                                    style="display: inline;width: 150px;"
                                                                    type="number"
                                                                    min="0"
                                                                    max="100"
                                                                    name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][rating]"
                                                                    maxlength="3"
                                                                    value="{{ $studentProfile->dayRatingList[$ratingDay->day_num] ?? '' }}">
                                                        </div>
                                                        <div>
                                                            <label for="">Дата</label>
                                                            <input
                                                                    type="date"
                                                                    class="form-control"
                                                                    style="display: inline;width: 150px;"
                                                                    min="0"
                                                                    max="100"
                                                                    name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][date]"
                                                                    maxlength="3"
                                                                    value="{{ $studentProfile->dayRatingList[$ratingDay->day_num] ?? '' }}">
                                                        </div>
                                                    </td>
                                                @endforeach
                                                @endif

                                                <td>
                                                    <?php $weekField = "week{$i}_result";?>

                                                    <label for="">Оценка</label>
                                                    <input
                                                            class="form-control"
                                                            style="display: inline;width: 70px;"
                                                            type="number"
                                                            min="0"
                                                            max="100"
                                                            name="data[{{$studentProfile->user_id}}][week{{$i}}_result]"
                                                            value="{{$studentProfile->studentDiscipline->$weekField}}"
                                                            maxlength="3">
                                                </td>
                                            @endfor


                                            <td>
                                                {{--  SRO  --}}
                                                @if($studentProfile->studentDiscipline->manualSROAccess)
                                                    <input class="form-control" style="width: 80px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][sro]" value="{{$studentProfile->studentDiscipline->task_result}}">
                                                @endif

                                                {{-- Available --}}
                                                @if($studentProfile->studentDiscipline->manualSROAvailable)
                                                    {{-- QR Error --}}
                                                    @if(false && $studentProfile->studentDiscipline->QRsCount < $studentProfile->studentDiscipline->minSROQRCount)
                                                        <div>Недостаточно посещений: {{$studentProfile->studentDiscipline->QRsCount}} из {{$studentProfile->studentDiscipline->minSROQRCount}}</div>
                                                    @endif

                                                    {{-- Not bought Error --}}
                                                    @if(!$studentProfile->studentDiscipline->payed)
                                                        <div>Нет купленных кредитов</div>
                                                    @endif
                                                @elseif($studentProfile->studentDiscipline->task_result !== null)
                                                    {{$studentProfile->studentDiscipline->task_result}} ({{$studentProfile->studentDiscipline->task_result_letter}})
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Exam --}}
                                                @if($isManualExamTime && $studentProfile->studentDiscipline->manualExamAccess)
                                                    <input class="form-control" style="width: 80px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][exam]" value="{{$studentProfile->studentDiscipline->test_result}}">
                                                @endif

                                                {{-- Available --}}
                                                @if($studentProfile->studentDiscipline->manualExamAvailable)
                                                    {{-- QR Error --}}
                                                    @if( false &&
    !$studentProfile->studentDiscipline->discipline->is_practice &&
    $studentProfile->studentDiscipline->QRsCount < $studentProfile->studentDiscipline->minExamQRCount)

                                                        <div>Недостаточно посещений: {{$studentProfile->studentDiscipline->QRsCount}} из {{$studentProfile->studentDiscipline->minExamQRCount}}</div>
                                                    @endif

                                                    {{-- Not bought Error --}}
                                                    @if(!$studentProfile->studentDiscipline->payed_credits)
                                                        <div>Нет купленных кредитов</div>
                                                    @endif
                                                @elseif($studentProfile->studentDiscipline->test_result !== null)
                                                    {{$studentProfile->studentDiscipline->test_result}} ({{$studentProfile->studentDiscipline->test_result_letter}})
                                                @endif
                                            </td>
                                            <td>
                                                {{-- Final --}}
                                                @if($studentProfile->studentDiscipline->final_result !== null)
                                                    {{$studentProfile->studentDiscipline->final_result}} ({{$studentProfile->studentDiscipline->final_result_letter}})
                                                @endif
                                            </td>
                                        @endif
                                        @if($discipline->is_practice)
                                        <td>
                                            @if(count($studentProfile->user->practiceStudentFilesByDiscipline($discipline->id)) > 0)
                                            <a onclick="showFiles({{ $studentProfile->user_id }})" style="cursor: pointer;"><i class="fa fa-paperclip" style="font-size: 1.2em"></i></a>
                                            <div id="file_list_{{ $studentProfile->user_id }}" style="display: none;">
                                                <ul>
                                                @foreach($studentProfile->user->practiceStudentFilesByDiscipline($discipline->id) as $studentDoc)
                                                    <li>
                                                        @if($studentDoc->type == 'file')
                                                        <a target="_blank" href="/syllabus_documents/student_files/{{ $studentDoc->file_name }}" style="cursor: pointer;">
                                                            {{ $studentDoc->original_name }}
                                                        </a>
                                                        @endif
                                                        @if($studentDoc->type == 'link')
                                                            <a target="_blank" href="{{ $studentDoc->link }}" style="cursor: pointer;">
                                                                {{ $studentDoc->link }}
                                                            </a>
                                                        @endif
                                                    </li>
                                                @endforeach
                                                </ul>
                                            </div>
                                            @else
                                                {{ __('No') }}
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            </div>
                            <input type="submit" class="btn btn-lg btn-primary" value="Сохранить">
                        </form>
                        @else
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                @foreach($discipline->getDisciplineSemestersByStudyForm() as $semester)
                                    <li class="nav-item {{ $currentSemester === $semester->semester ? 'active': ''}}">
                                        <a class="nav-link" id="home-tab" data-toggle="tab" href="#group-{{$semester->id}}" role="tab" aria-controls="home" aria-selected="true">
                                            {{$semester->semester}}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                @foreach($discipline->getDisciplineSemestersByStudyForm() as $semester)
                                    <?php
                                        $disable = $currentSemester !== $semester->semester;
                                    ?>
                                    <div class="tab-pane {{!$disable ? 'active': ''}}" id="group-{{$semester->id}}" role="tabpanel" aria-labelledby="home-tab">
                                        <form action="{{route('teacherGroupSave', ['group_id' => $group->id, 'discipline_id' => $discipline->id])}}" method="post">
                                            {{csrf_field()}}
                                            <div style="height: 85vh;">
                                                <table id="data-table"
                                                       class="table table-striped table-bordered table-hover dt-responsive">
                                                    <thead>
                                                    <tr>
                                                        <th rowspan="2" style="z-index:1">#</th>
                                                        <th rowspan="2" style="z-index:1">ФИО</th>
                                                        @if(!$discipline->is_practice)
                                                            <th rowspan="2"></th>
                                                        @endif

                                                        @for($i=1; $i<=6; $i++)
                                                            @if(isset($dayCountByWeek[$i]))
                                                                <th colspan="{{ count($dayCountByWeek[$i])+1 }}">Неделя {{ $i }}</th>
                                                            @else
                                                                <th>Неделя {{ $i }}</th>
                                                            @endif
                                                        @endfor
                                                        <th rowspan="2">Р1 (T1)</th>
                                                        @for($i=8; $i<=20; $i++)
                                                            @if(isset($dayCountByWeek[$i]))
                                                                <th colspan="{{ count($dayCountByWeek[$i])+1 }}">Неделя {{ $i }}</th>
                                                            @else
                                                                <th>Неделя {{ $i }}</th>
                                                            @endif
                                                        @endfor
                                                        <th rowspan="2">Р2 (СРО)</th>
                                                        <th rowspan="2">Экзамен / Практика</th>
                                                        <th rowspan="2">Финальная оценка</th>
                                                        @if($discipline->is_practice)
                                                            <th rowspan="2">{{ __('Files (practice)') }}</th>
                                                        @endif

                                                    </tr>
                                                    <tr>

                                                        @for($i=1; $i<=6; $i++)
                                                            @if(isset($dayCountByWeek[$i]))
                                                                @foreach($dayCountByWeek[$i] as $k => $ratingDay)

                                                                    <th>занятие {{ $ratingDay->dayOffset }}</th>

                                                                @endforeach
                                                            @endif
                                                            <th>СРО</th>
                                                        @endfor

                                                        @for($i=8; $i<=20; $i++)
                                                            @if(isset($dayCountByWeek[$i]))
                                                                @foreach($dayCountByWeek[$i] as $k => $ratingDay)

                                                                    <th>занятие {{ $ratingDay->dayOffset }}</th>

                                                                @endforeach
                                                            @endif
                                                            <th>СРО</th>
                                                        @endfor

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($studentsProfiles as $i => $studentProfile)
                                                        <?php
                                                            $studentDays = $studentProfile->getDisciplineDays($discipline->id, Auth::id(), $semester->semester);
                                                        ?>
                                                        <tr>
                                                            <td>{{$i + 1}}</td>
                                                            <td>
                                                                {{$studentProfile->fio}} {{$studentProfile->user_id}}
                                                            </td>
                                                            @if(!$discipline->is_practice)
                                                                <td>

                                                                    <a v-on:click="showFiles({{ $studentProfile->user_id }})" style="cursor: pointer;" v-bind:class="{'clip-file-disable': !studenFilesCount({{ $studentProfile->user_id }})}">

                                                                        <i v-if="newStudenFilesCount({{$studentProfile->user_id}}) > 0" class="fa fa-paperclip badge" style="font-size: 1.2em; background-color: #30638e;">
                                                                            + <span v-html="newStudenFilesCount({{$studentProfile->user_id}})"></span>
                                                                        </i>

                                                                        <i v-if="newStudenFilesCount({{$studentProfile->user_id}}) == 0" class="fa fa-paperclip" style="font-size: 1.2em"></i>

                                                                    </a>

                                                                </td>
                                                            @endif
                                                            @if(empty($studentProfile->studentDiscipline))
                                                                <td colspan="6">
                                                                    <p class="text-danger">Ошибка. У студента отсутствует данная дисциплина.</p>
                                                                </td>
                                                            @else

                                                                @for($i = 1; $i <= 6; $i++)
                                                                    @if(isset($dayCountByWeek[$i]))
                                                                        @foreach($dayCountByWeek[$i] as $ratingDay)
                                                                            <td>
                                                                                <div>
                                                                                    <label for="">Оценка</label>
                                                                                    <input
                                                                                            {{$disable ? 'disabled' : ''}}
                                                                                            class="form-control"
                                                                                            style="display: inline;width: 150px;"
                                                                                            type="number"
                                                                                            min="0"
                                                                                            max="100"
                                                                                            name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][rating]"
                                                                                            maxlength="3"
                                                                                            value="{{ $studentDays->where('day_num', $ratingDay->day_num)->first()->rating ?? '' }}">
                                                                                </div>
                                                                                <div>
                                                                                    <label for="">Дата</label>
                                                                                    <input
                                                                                            {{$disable ? 'disabled' : ''}}
                                                                                            type="date"
                                                                                            class="form-control"
                                                                                            style="display: inline;width: 150px;"
                                                                                            min="0"
                                                                                            max="100"
                                                                                            name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][date]"
                                                                                            maxlength="3"
                                                                                            value="{{ $studentDays->where('day_num', $ratingDay->day_num)->first()->date ?? '' }}">
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    @endif

                                                                    <td>
                                                                        <?php $weekField = "week{$i}_result";?>
                                                                        <label for="">Оценка</label>
                                                                        <input
                                                                                {{$disable ? 'disabled' : ''}}
                                                                                class="form-control"
                                                                                style="display: inline;width: 70px;"
                                                                                type="number"
                                                                                min="0"
                                                                                max="100"
                                                                                name="data[{{$studentProfile->user_id}}][week{{$i}}_result]"
                                                                                value="{{$studentProfile->studentDiscipline->$weekField}}"
                                                                                maxlength="3">
                                                                    </td>
                                                                @endfor

                                                                <td>
                                                                    {{--                                                <input class="form-control" style="display: inline;width: 70px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][test1_result]" value="{{$studentProfile->studentDiscipline->test1_result}}" maxlength="3">--}}

                                                                    {{--                                                @if($studentProfile->user_id==8251) {{dd($studentProfile->studentDiscipline)}} @endif--}}

                                                                    @if($studentProfile->studentDiscipline->test1_result !== null)
                                                                        {{$studentProfile->studentDiscipline->test1_result}}
                                                                        ({{$studentProfile->studentDiscipline->test1_result_letter}})
                                                                    @endif
                                                                </td>


                                                                @for($i = 8; $i <= 20; $i++)
                                                                    @if(isset($dayCountByWeek[$i]))
                                                                        @foreach($dayCountByWeek[$i] as $ratingDay)
                                                                            <td>
                                                                                <div>
                                                                                    <label for="">Оценка</label>
                                                                                    <input
                                                                                            {{$disable ? 'disabled' : ''}}
                                                                                            class="form-control"
                                                                                            style="display: inline;width: 150px;"
                                                                                            type="number"
                                                                                            min="0"
                                                                                            max="100"
                                                                                            name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][rating]"
                                                                                            maxlength="3"
                                                                                            value="{{ $studentProfile->dayRatingList[$ratingDay->day_num] ?? '' }}">
                                                                                </div>
                                                                                <div>
                                                                                    <label for="">Дата</label>
                                                                                    <input
                                                                                            {{$disable ? 'disabled' : ''}}
                                                                                            type="date"
                                                                                            class="form-control"
                                                                                            style="display: inline;width: 150px;"
                                                                                            min="0"
                                                                                            max="100"
                                                                                            name="data[{{$studentProfile->user_id}}][day_rating][{{$ratingDay->day_num}}][date]"
                                                                                            maxlength="3"
                                                                                            value="{{ $studentProfile->dayRatingList[$ratingDay->day_num] ?? '' }}">
                                                                                </div>
                                                                            </td>
                                                                        @endforeach
                                                                    @endif

                                                                    <td>
                                                                        <?php $weekField = "week{$i}_result";?>

                                                                        <label for="">Оценка</label>
                                                                        <input
                                                                                {{$disable ? 'disabled' : ''}}
                                                                                class="form-control"
                                                                                style="display: inline;width: 70px;"
                                                                                type="number"
                                                                                min="0"
                                                                                max="100"
                                                                                name="data[{{$studentProfile->user_id}}][week{{$i}}_result]"
                                                                                value="{{$studentProfile->studentDiscipline->$weekField}}"
                                                                                maxlength="3">
                                                                    </td>
                                                                @endfor


                                                                <td>
                                                                    {{--  SRO  --}}
                                                                    @if($studentProfile->studentDiscipline->manualSROAccess)
                                                                        <input class="form-control" style="width: 80px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][sro]" value="{{$studentProfile->studentDiscipline->task_result}}">
                                                                    @endif

                                                                    {{-- Available --}}
                                                                    @if($studentProfile->studentDiscipline->manualSROAvailable)
                                                                        {{-- QR Error --}}
                                                                        @if(false && $studentProfile->studentDiscipline->QRsCount < $studentProfile->studentDiscipline->minSROQRCount)
                                                                            <div>Недостаточно посещений: {{$studentProfile->studentDiscipline->QRsCount}} из {{$studentProfile->studentDiscipline->minSROQRCount}}</div>
                                                                        @endif

                                                                        {{-- Not bought Error --}}
                                                                        @if(!$studentProfile->studentDiscipline->payed)
                                                                            <div>Нет купленных кредитов</div>
                                                                        @endif
                                                                    @elseif($studentProfile->studentDiscipline->task_result !== null)
                                                                        {{$studentProfile->studentDiscipline->task_result}} ({{$studentProfile->studentDiscipline->task_result_letter}})
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{-- Exam --}}
                                                                    @if($isManualExamTime && $studentProfile->studentDiscipline->manualExamAccess)
                                                                        <input class="form-control" style="width: 80px;" type="number" min="0" max="100" name="data[{{$studentProfile->user_id}}][exam]" value="{{$studentProfile->studentDiscipline->test_result}}">
                                                                    @endif

                                                                    {{-- Available --}}
                                                                    @if($studentProfile->studentDiscipline->manualExamAvailable)
                                                                        {{-- QR Error --}}
                                                                        @if( false &&
                        !$studentProfile->studentDiscipline->discipline->is_practice &&
                        $studentProfile->studentDiscipline->QRsCount < $studentProfile->studentDiscipline->minExamQRCount)

                                                                            <div>Недостаточно посещений: {{$studentProfile->studentDiscipline->QRsCount}} из {{$studentProfile->studentDiscipline->minExamQRCount}}</div>
                                                                        @endif

                                                                        {{-- Not bought Error --}}
                                                                        @if(!$studentProfile->studentDiscipline->payed_credits)
                                                                            <div>Нет купленных кредитов</div>
                                                                        @endif
                                                                    @elseif($studentProfile->studentDiscipline->test_result !== null)
                                                                        {{$studentProfile->studentDiscipline->test_result}} ({{$studentProfile->studentDiscipline->test_result_letter}})
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    {{-- Final --}}
                                                                    @if($studentProfile->studentDiscipline->final_result !== null)
                                                                        {{$studentProfile->studentDiscipline->final_result}} ({{$studentProfile->studentDiscipline->final_result_letter}})
                                                                    @endif
                                                                </td>
                                                            @endif
                                                            @if($discipline->is_practice)
                                                                <td>
                                                                    @if(count($studentProfile->user->practiceStudentFilesByDiscipline($discipline->id)) > 0)
                                                                        <a onclick="showFiles({{ $studentProfile->user_id }})" style="cursor: pointer;"><i class="fa fa-paperclip" style="font-size: 1.2em"></i></a>
                                                                        <div id="file_list_{{ $studentProfile->user_id }}" style="display: none;">
                                                                            <ul>
                                                                                @foreach($studentProfile->user->practiceStudentFilesByDiscipline($discipline->id) as $studentDoc)
                                                                                    <li>
                                                                                        @if($studentDoc->type == 'file')
                                                                                            <a target="_blank" href="/syllabus_documents/student_files/{{ $studentDoc->file_name }}" style="cursor: pointer;">
                                                                                                {{ $studentDoc->original_name }}
                                                                                            </a>
                                                                                        @endif
                                                                                        @if($studentDoc->type == 'link')
                                                                                            <a target="_blank" href="{{ $studentDoc->link }}" style="cursor: pointer;">
                                                                                                {{ $studentDoc->link }}
                                                                                            </a>
                                                                                        @endif
                                                                                    </li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </div>
                                                                    @else
                                                                        {{ __('No') }}
                                                                    @endif
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <input type="submit" class="btn btn-lg btn-primary" value="Сохранить">
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="modal" tabindex="-1" role="dialog" aria-labelledby="" id="filesModal">
                <div class="modal-dialog modal-lg " style="min-width:950px;" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" v-on:click="closeFiles()"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Список файлов</h4>
                        </div>
                        <div class="modal-body col-sm-12" style="overflow-y: auto;max-height: 75vh;">
                            <div id="modal-file-list">
                                <table class="table">
                                    <tr v-for="(file, index) in studentFiles[studentFilesActiveUser]" v-bind:class="{'new-student-file': file.new_file}">
                                        <td>
                                            <a v-bind:href="file.link" v-on:click="readStudentFile(index)" target="_blank" >@{{file.name}}</a>
                                        </td>
                                        <td>
                                            <a
                                                    class="btn btn-primary pull-right"
                                                    v-show="file.can_delete"
                                                    v-on:click="deleteStudentFile(index)">{{ __('Delete') }}
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            @if(!$discipline->is_practice)
                                <form id="upload-student-file-form">
                                <a class="btn btn-primary" v-on:click="addFileClick()">{{ __('Add file') }}</a>
                                <input type="file" name="document" v-on:change="uploadStudentFile()" id="new_student_file" style="display:none;" />
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
{{--    </div>--}}

@endsection

@section('scripts')

    <script type="text/javascript">


        var app = new Vue({
            el: '#main',
            data: {
                studentFiles: [],
                studentFilesActiveUser: 0,
                errorMessage: ''
            },
            methods: {
                showFiles: function(userId) {
                    this.studentFilesActiveUser = userId;
                    $('#filesModal').show();
                },

                closeFiles: function() {
                    this.studentFilesActiveUser = 0;
                    $('#filesModal').hide();
                },

                uploadStudentFile: function () {

                    var self = this;
                    var frm = $('#upload-student-file-form')

                    let formData = new FormData(frm[0]);

                    formData.append('student_id', this.studentFilesActiveUser);
                    formData.append('_token', "{{ csrf_token() }}");

                    axios.post('{{ route('journalUploadStudentFile', ['discipline_id' => $discipline->id]) }}',
                        formData, {
                            headers: {
                                'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(function(response){
                            if(response.data.status == 'success'){

                                if(!self.studentFiles[self.studentFilesActiveUser])
                                    self.studentFiles[self.studentFilesActiveUser] = [];

                                self.studentFiles[self.studentFilesActiveUser].push({
                                    id: response.data.id,
                                    name: response.data.original_name,
                                    link: '/syllabus_documents/student_files/' + response.data.file_name,
                                    can_delete: true,
                                    new_file: false
                                });

                                self.$forceUpdate();

                            } else {
                                alert(response.data.message);
                            }
                        });
                },
                addFileClick: function () {
                    $('#new_student_file').click();
                },
                deleteStudentFile:function (fileIndex) {

                    var self = this;

                    axios.post('{{ route('journalDeleteStudentFile') }}', {
                        file_id: this.studentFiles[self.studentFilesActiveUser][fileIndex].id
                    })
                        .then(function(response){
                            if(response.data.status == 'success'){
                                self.studentFiles[self.studentFilesActiveUser].splice(fileIndex, 1);
                                self.$forceUpdate();
                            } else {
                                alert(response.data.message);
                            }
                        });
                },
                readStudentFile: function (fileIndex) {

                    var self = this;
                    var fIndex = fileIndex;

                    axios.post('{{ route('journalSetReadStudentFile') }}', {
                        file_id: this.studentFiles[self.studentFilesActiveUser][fileIndex].id
                    })
                        .then(function(response){
                            if(response.data.status == 'success'){
                                self.studentFiles[self.studentFilesActiveUser][fIndex].new_file = false;
                                self.$forceUpdate();
                            } else {
                                alert(response.data.message);
                            }
                        });
                },
                newStudenFilesCount: function (studentId) {

                    var newCount = 0;

                    if(this.studentFiles[studentId] != undefined) {
                        for (var i = 0; i < this.studentFiles[studentId].length; i++) {
                            if (this.studentFiles[studentId][i].new_file)
                                newCount++;
                        }
                    }

                    return newCount;
                },
                studenFilesCount: function (studentId) {
                    if(this.studentFiles[studentId]) {
                        return this.studentFiles[studentId].length;
                    }

                    return 0;
                }
            },
            created: function(){
                @if(!$discipline->is_practice)
                @foreach($studentsProfiles as $studentProfile)

                @if(count($studentProfile->user->disciplineStudentFilesByDiscipline($discipline->id)) > 0)
                    this.studentFiles[{{ $studentProfile->user_id }}] = [];
                    @foreach($studentProfile->user->disciplineStudentFilesByDiscipline($discipline->id) as $i => $file)
                        @if($file->type == 'file')
                            this.studentFiles[{{ $studentProfile->user_id }}].push({
                                id: {{ $file->id }},
                                name: '{{ $file->original_name }}',
                                link: '/syllabus_documents/student_files/{{ $file->file_name }}',
                                can_delete: {{ $file->canDeleteByTeacher(\App\Services\Auth::user()->id) ? 'true' : 'false' }},
                                new_file: {{ $file->new_file ? 'true' : 'false' }}
                            });
                        @elseif($file->type == 'link')
                            this.studentFiles[{{ $studentProfile->user_id }}].push({
                                id: {{ $file->id }},
                                name: '{{ $file->link }}',
                                link: '{{ $file->link }}',
                                can_delete: {{ $file->canDeleteByTeacher(\App\Services\Auth::user()->id) ? 'true' : 'false' }},
                                new_file: {{ $file->new_file ? 'true' : 'false' }}
                            });
                        @endif

                    @endforeach
                @endif

                @endforeach
                @endif
            }
        });


        $(function(){
            $("#data-table").tableHeadFixer({
                head: true,
                foot: false,
                left: 2,
                right: 0,
                'z-index': 0
            });
        });

    </script>

@endsection