<p>Вас пригласили в качестве эксперта для рассмотрения аппеляции №{{$appeal->id}};

<div><strong>ID апелляции:</strong> {{$appeal->id}}</div>
<div><strong>Студент:</strong> {{$appeal->user->studentProfile->fio}}</div>
<div><strong>Специальность:</strong> {{$appeal->user->studentProfile->speciality->name}} ($appeal->user->studentProfile->speciality->year)</div>
<div><strong>Базовое образованиее:</strong> {{__($appeal->user->base_education)}}</div>
<div><strong>Форма обучения:</strong> {{__($appeal->user->studentProfile->education_study_form)}}</div>

<div><strong>Дисциплина:</strong> {{$appeal->studentDiscipline->discipline->name}}</div>
<div><strong>Вид контроля:</strong> {{__('appeal_type_'. $appeal->type)}}</div>
<div><strong>Дата, время сдачи:</strong> {{$appeal->control_date->format('d.m.Y H:i')}}</div>
<div><strong>Результат:</strong> {{$appeal->control_result}}%, {{$appeal->control_result_points}} ({{$appeal->control_result_letter}})</div>

<div><strong>Заявление:</strong> {{$appeal->reason}}</div>

<br><br>
<div><a href="{{route('adminAppealReview', ['id' => $appeal->id])}}">Рассмотреть аппеляцию</a></div>