
<div class="col-md-12 alert alert-info">
    <div>Засчитанно бесплатно: <span class="credits-allowed-got">{{$migratedCredits}}</span> из <span class="credits-allowed">{{$maxCreditsAllowed == -1 ? '∞' : $maxCreditsAllowed}}</span> кредитов
    </div>
    <div>Засчитано с требованием оплаты: <span class="pay-req-credits-allowed-got">{{$payReqMigratedCredits}}</span> из <span class="pay-req-credits-allowed">∞{{--$payReqMaxCreditsAllowed--}}</span> кредитов
    </div>
</div>

<table id="data-table-dispciplines" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th>Id</th>
        <th>Наименование</th>
        <th>Кредиты</th>
        <th>Куплено</th>
        <th>Скидка, кредиты</th>
        <th>Реком. семестр</th>
        <th>Куплена в семестре</th>
        <th>План</th>
        <th>Т1</th>
        <th>Т2</th>
        <th>СРО</th>
        <th>Курсовая</th>
        <th>Практика</th>
        <th>Итоговая оценка</th>
    </tr>
    </thead>

    <tbody>
    @foreach($student->disciplines as $i => $discipline)
        <tr>
            <td>{{$discipline->id}}</td>
            <td class="td-extendable">
                @if(\App\Services\Auth::user()->hasRight('or_cabinet','edit'))
                    <div class="td-extendable-button"><i class="plusIcon">+</i> {{$discipline->name}}</div>

                    @if(in_array(\App\Services\Auth::user()->id, [8579, 96, 18365, 15546, 4876]))
                        <div class="after-extend" style="display: none;">
                            @if ($maxCreditsAllowed == -1 || $maxCreditsAllowed != 0)
                                {{-- Has payed credits --}}
                                @if($discipline->pivot->payed_credits > 0)
                                    <div class="col-md-12 alert alert-warning" style="padding:3px; margin: 5px 0;">Куплены кредиты. Невозможно засчитать</div>
                                    <div class="col-md-2"><input class="form-control" type="text" disabled></div>
                                    <span class="btn btn-success disabled">Засчитать дисциплину бесплатно</span>
                                    <span class="btn btn-info disabled">Засчитать с требованием оплаты</span>
                                @else
                                    <div class="form-inline" style="margin: 7px 0 0 15px;">
                                        Результат: <input class="form-control" type="number" min="0" max="100" name="points-{{$discipline->id}}" style="width:80px;">
                                        <span id="add-discipline-{{ $discipline->id }}" class="btn btn-success add-discipline" ects={{$discipline->ects}} disciplineId={{$discipline->id}}>Засчитать дисциплину бесплатно</span>
                                        <span id="add-pay-req-discipline-{{$discipline->id}}" class="btn btn-info add-pay-req-discipline" ects={{$discipline->ects}} disciplineId={{$discipline->id}}>Засчитать с требованием оплаты</span>
                                    </div>
                                @endif
                            @endif

                            <div class="form-inline" style="margin: 7px 0 0 15px;">
                                Скидка
                                <input id="free-credits-{{ $discipline->id }}" type="number" class="form-control" min="0" max="{{ $discipline->ects }}"
                                       value="{{ $discipline->pivot->free_credits }}" style="width: 60px;text-align: right;">
                                кредитов
                                <input type="button" class="btn btn-warning change-free-credits" id="free-credits-btn-{{ $discipline->id }}"
                                       data-discipline-id="{{ $discipline->id }}" data-max-credits="{{ $discipline->ects }}" value="Изменить">
                            </div>
                        </div>
                    @endif
                @else
                    <div class="td-extendable-button"> {{ $discipline->name }}</div>
                @endif
            </td>
            <td>{{$discipline->ects}}</td>
            <td>
                @if($discipline->pivot->payed)
                    да, {{(int)$discipline->pivot->payed_credits}} кредитов
                @elseif (!empty($discipline->pivot->payed_credits))
                    частично, {{(int)$discipline->pivot->payed_credits}} кредитов
                @endif
            </td>
            <td>
                <span id="span-free-credits-{{ $discipline->id }}">{{ $discipline->pivot->free_credits }}</span>
            </td>
            <td>
                <span class="badge"
                        @if($discipline->pivot->recommended_semester % 2)
                            style="background-color: #0e6dcd;"
                        @else
                            style="background-color: orange;"
                        @endif>
                    {{$discipline->pivot->recommended_semester}}
                </span>
            </td>
            <td>{{ $discipline->pivot->at_semester }}</td>
            <td>{{ $discipline->pivot->plan_semester }}</td>
            <td>
                @if($discipline->pivot->test1_result !== null)
                    {{ $discipline->pivot->test1_result }} &nbsp;
                    @if($discipline->pivot->test1_result_letter)
                        ({{ $discipline->pivot->test1_result_letter }})
                    @endif
                @endif
            </td>
            <td>
                @if($discipline->pivot->test_result !== null)
                    {{ $discipline->pivot->test_result }} &nbsp;
                    @if($discipline->pivot->test_result_letter)
                        ({{ $discipline->pivot->test_result_letter }})
                    @endif
                @endif
            </td>
            <td>
                @if($discipline->pivot->task_result !== null)
                    {{ $discipline->pivot->task_result }} &nbsp;
                    @if($discipline->pivot->task_result_letter)
                        ({{ $discipline->pivot->task_result_letter }})
                    @endif
                @endif
            </td>
            <td>
                @if($discipline->hasCourseworkByUserId($student->id))
                    Да
                @else
                    Нет
                @endif
            </td>
            <td>
                @if($discipline->is_practice)
                    Да
                @else
                    Нет
                @endif
            </td>
            <td id="test-result-{{ $discipline->id }}">
                @if($discipline->pivot->final_result !== null)
                    {{ $discipline->pivot->final_result }} &nbsp;
                    @if($discipline->pivot->final_result_letter)
                        ({{ $discipline->pivot->final_result_letter }})
                    @endif
                    <a href="{{ route('adminStudentShowResult', ['id'=> $student->id, 'disciplineId'=>$discipline->id]) }}">Ответы</a>

                    @if(in_array(\App\Services\Auth::user()->id, [96, 18365, 15546, 4876]))
                        <a onclick="deleteResult({{$discipline->id}})" style="cursor: pointer">Удалить</a>
                    @endif
                @endif

                @if($discipline->pivot->migrated)
                    @if(\App\StudentDiscipline::getMigratedType($discipline->pivot->migrated, $discipline->pivot->payed, $discipline->pivot->payed_credits) == \App\StudentDiscipline::MIGRATED_TYPE_FREE)
                        <span class="label label-success">Засчитано бесплатно</span>
                    @else
                        <span class="label label-info">Засчитано с требованием оплаты</span>
                    @endif
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
    