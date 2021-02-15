@extends("admin.admin_app")

@section('title', 'Апелляции')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Апелляции</h2>

            <a href="{{URL::route('adminAppealList')}}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                {!! Form::open(['url' => route('adminAppealReviewPost', ['id' => $appeal->id]), 'class' => '', 'name' => 'service_form', 'id' => 'service_form', 'role' => 'form','enctype' => 'multipart/form-data']) !!}

                <div><strong>ID апелляции:</strong> {{$appeal->id}}</div>
                <div><strong>Статус:</strong>
                    @if($appeal->status == \App\Appeal::STATUS_REVIEW)
                        <span class="label label-warning">@lang('appeal_status_' . $appeal->status)</span>
                    @elseif($appeal->status == \App\Appeal::STATUS_APPROVED)
                        <span class="label label-success">@lang('appeal_status_' . $appeal->status)</span>
                    @else
                        <span class="label label-danger">@lang('appeal_status_' . $appeal->status)</span>
                    @endif
                </div>
                <div><strong>Студент:</strong> <a href="{{route('adminStudentEdit', ['id' => $appeal->user->id])}}">{{$appeal->user->studentProfile->fio}}</a></div>
                <div><strong>Специальность:</strong> <a href="{{route('specialityEdit', ['id' => $appeal->user->studentProfile->speciality->id])}}">{{$appeal->user->studentProfile->speciality->name}} ({{$appeal->user->studentProfile->speciality->year}})</a></div>
                <div><strong>Базовое образование:</strong> @lang($appeal->user->base_education)</div>
                <div><strong>Форма обучения:</strong> @lang($appeal->user->studentProfile->education_study_form)</div>

                <div><strong>Дисциплина:</strong> <a href="{{route('disciplineEdit', ['id' => $appeal->studentDiscipline->discipline->id])}}">{{$appeal->studentDiscipline->discipline->name}}</a></div>
                <div><strong>Вид контроля:</strong> @lang('appeal_type_'. $appeal->type)</div>
                <div><strong>Дата, время сдачи:</strong> {{$appeal->control_date->format('d.m.Y H:i')}}</div>
                <div><strong>Результат:</strong> {{$appeal->control_result}}%, {{$appeal->control_result_points}} ({{$appeal->control_result_letter}})</div>
                <div><strong>Дата подачи апелляции:</strong> {{$appeal->created_at->format('d.m.Y H:i')}}</div>

                <div class="panel panel-default">
                    <div class="panel-heading">Заявление:</div>
                    <div class="panel-body">{{$appeal->reason}}</div>
                </div>

                <h2>Срез вопросов и ответов на момент апелляции</h2>
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>Вопрос</th>
                        <th>Ответы</th>
                    </tr>

                    <?php
                    $n = 1;
                    ?>
                    @foreach($appeal->answers->snapshot as $question)
                        <tr>
                            <td>{{$n++}}</td>
                            <td>{!!$question['question']!!}</td>
                            <td>
                                @foreach($question['answers'] as $answer)
                                    <div>
                                        @if($question['multiple_answers'])
                                            <input type="checkbox" disabled @if(in_array($answer['id'], $question['user_answers'])) checked @endif />
                                        @else
                                            <input type="radio" disabled @if(in_array($answer['id'], $question['user_answers'])) checked @endif />
                                        @endif

                                        @if ($answer['correct'])<strong>@endif

                                        {!! strip_tags($answer['answer']) !!}

                                        @if ($answer['correct']) <u>(верный, {{$answer['points']}} баллов)</u></strong> @endif
                                    </div>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                </table>

                @if(!empty($appeal->file))
                    <div><img src="{{Storage::disk('appeals')->url($appeal->file)}}"></div>
                @endif

                <h2>Эксперт 1</h2>

                @if(empty($appeal->expert1_id))
                    <div class="form-group">
                        <label for="resolution">Текст решения</label>
                        <textarea name="resolution_text" class="form-control" id="resolution"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="resolution">Сообщить эксперту</label>
                        <select name="expert3_id" class="form-control">
                            <option value=""></option>
                            @foreach($teachers as $teacher)
                                <option value="{{$teacher->id}}">{{$teacher->fio}}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="submit" name="approve" value="Одобрить" class="btn btn-success" onclick="return confirm('Подтверждаете одобрение?');">
                    <input type="submit" value="Отказать" class="btn btn-danger" onclick="return confirm('Подтверждаете отказ?');">
                @else
                    <div><strong>Эксперт:</strong> <a href="{{route('adminTeacherEdit', ['id' => $appeal->expert1->id])}}">{{$appeal->expert1->fio}}</a></div>
                    <div>
                        <strong>Решение:</strong>
                        @if($appeal->expert1_resolution == \App\Appeal::RESOLUTION_APPROVED)
                            <span class="label label-success">@lang('appeal_resolution_' . $appeal->expert1_resolution)</span>
                        @else
                            <span class="label label-danger">@lang('appeal_resolution_' . $appeal->expert1_resolution)</span>
                        @endif
                    </div>
                    <div><strong>Текст решения:</strong> {{$appeal->expert1_resolution_text}}</div>
                    <div><strong>Дата решения:</strong> {{$appeal->expert1_resolution_date->format('d.m.Y H:i')}}</div>
                @endif

                @if (!empty($appeal->expert1_id) && $appeal->status != \App\Appeal::STATUS_DECLINED)
                    <h2>Эксперт 2</h2>

                    @if(empty($appeal->expert2_id))
                        @if($appeal->expert1_id != Auth::user()->id && $appeal->expert3_id != Auth::user()->id)
                            <div class="form-group">
                                <label for="resolution">Текст решения</label>
                                <textarea name="resolution_text" class="form-control" id="resolution"></textarea>
                            </div>

                            @if(empty($appeal->expert3_id))
                                <div class="form-group">
                                    <label for="resolution">Сообщить эксперту</label>
                                    <select name="expert3_id" class="form-control">
                                        <option value=""></option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{$teacher->id}}">{{$teacher->fio}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            <input type="submit" name="approve" value="Одобрить" class="btn btn-success" onclick="return confirm('Подтверждаете одобрение?');">
                            <input type="submit" value="Отказать" class="btn btn-danger" onclick="return confirm('Подтверждаете отказ?');">
                        @else
                            Ожидается решение.
                        @endif
                    @else
                        <div><strong>Эксперт:</strong> <a href="{{route('adminTeacherEdit', ['id' => $appeal->expert2->id])}}">{{$appeal->expert2->fio}}</a></div>
                        <div>
                            <strong>Решение:</strong>
                            @if($appeal->expert2_resolution == \App\Appeal::RESOLUTION_APPROVED)
                                <span class="label label-success">@lang('appeal_resolution_' . $appeal->expert2_resolution)</span>
                            @else
                                <span class="label label-danger">@lang('appeal_resolution_' . $appeal->expert2_resolution)</span>
                            @endif
                        </div>
                        <div><strong>Текст решения:</strong> {{$appeal->expert2_resolution_text}}</div>
                        <div><strong>Дата решения:</strong> {{$appeal->expert2_resolution_date->format('d.m.Y H:i')}}</div>
                    @endif
                @endif

                @if (!empty($appeal->expert3_id) && ($appeal->status != \App\Appeal::STATUS_DECLINED || !empty($appeal->expert3_resolution)))
                    <h2>Эксперт 3.</h2>
                    <div><strong>Эксперт:</strong> <a href="{{route('adminTeacherEdit', ['id' => $appeal->expert3->id])}}">{{$appeal->expert3->fio}}</a></div>

                    @if(empty($appeal->expert3_resolution))
                        @if($appeal->expert3_id == Auth::user()->id)
                            <div class="form-group">
                                <label for="resolution">Текст решения</label>
                                <textarea name="resolution_text" class="form-control" id="resolution"></textarea>
                            </div>

                            <input type="submit" name="approve" value="Одобрить" class="btn btn-success" onclick="return confirm('Подтверждаете одобрение?');">
                            <input type="submit" value="Отказать" class="btn btn-danger" onclick="return confirm('Подтверждаете отказ?');">
                        @else
                            Ожидается решение.
                        @endif
                    @else
                        <div>
                            <strong>Решение:</strong>
                            @if($appeal->expert3_resolution == \App\Appeal::RESOLUTION_APPROVED)
                                <span class="label label-success">@lang('appeal_resolution_' . $appeal->expert3_resolution)</span>
                            @else
                                <span class="label label-danger">@lang('appeal_resolution_' . $appeal->expert3_resolution)</span>
                            @endif
                        </div>
                        <div><strong>Текст решения:</strong> {{$appeal->expert3_resolution_text}}</div>
                        <div><strong>Дата решения:</strong> {{$appeal->expert3_resolution_date->format('d.m.Y H:i')}}</div>
                    @endif
                @endif

                {!! Form::close() !!}

                @if($appeal->status == \App\Appeal::STATUS_APPROVED)
                    <h2>Решение</h2>

                    @if(empty($appeal->resolution_action))
                        {!! Form::open(['url' => route('adminAppealAction', ['id' => $appeal->id]), 'class' => '', 'name' => 'service_form', 'id' => 'service_form', 'role' => 'form']) !!}
                        <div>
                            <label>
                                <input type="radio" name="action" value="{{\App\Appeal::RESOLUTION_ACTION_NEW_TRY}}">
                                Дать возможность пересдать
                            </label>
                        </div>
                        <div>
                            <label>
                                <input type="radio" name="action" value="{{\App\Appeal::RESOLUTION_ACTION_ADD_VALUE}}">
                                Добавить баллов
                            </label>
                        </div>

                        <div>Текущий результат: {{$appeal->control_result}}%. Добавить <input type="number" name="value" min="0" max="{{100 - $appeal->control_result}}" style="width: 80px;">%</div>

                        <button type="submit" class="btn btn-primary">Сохранить</button>
                        {!! Form::close() !!}
                    @else
                        <div>
                            Принял решение <a href="{{route('adminTeacherEdit', ['id' => $appeal->resolutionUser->id])}}">{{$appeal->resolutionUser->fio}}</a>
                        </div>

                        <div>
                            Принято решение - <strong>
                            @if($appeal->resolution_action == \App\Appeal::RESOLUTION_ACTION_NEW_TRY)
                                пересдача
                            @elseif($appeal->resolution_action == \App\Appeal::RESOLUTION_ACTION_ADD_VALUE)
                                добавлено {{$appeal->added_value }} баллов
                            @else
                                н/д
                            @endif
                            </strong>
                        </div>
                    @endif
                @endif
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">

    </script>
@endsection