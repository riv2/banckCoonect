@extends("admin.admin_app")

@section('title', 'Результат тестирования')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Результат тестирования</h2>

            <a href="{{URL::route('adminQuizResults')}}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <div><strong>ID результата:</strong> {{$quizResult->id}}</div>
                <div><strong>Вид контроля:</strong> {{$quizResult->type_text}}</div>
                <div><strong>Студент:</strong> <a href="{{route('adminStudentEdit', ['id' => $quizResult->user_id])}}">{{$quizResult->user->studentProfile->fio}}</a></div>
                <div><strong>Специальность:</strong> <a href="{{route('specialityEdit', ['id' => $quizResult->user->studentProfile->speciality->id])}}">{{$quizResult->user->studentProfile->speciality->name}} ({{$quizResult->user->studentProfile->speciality->year}})</a></div>
                <div><strong>Базовое образование:</strong> @lang($quizResult->user->base_education)</div>
                <div><strong>Форма обучения:</strong> @lang($quizResult->user->studentProfile->education_study_form)</div>

                <div><strong>Дисциплина:</strong> <a href="{{route('disciplineEdit', ['id' => $quizResult->discipline_id])}}">{{$quizResult->studentDiscipline->discipline->name}}</a></div>
                <div><strong>Вид контроля:</strong> {{$quizResult->type_text}}</div>
                <div><strong>Дата, время сдачи:</strong> {{$quizResult->created_at->format('d.m.Y H:i')}}</div>
                <div><strong>Результат:</strong> {{$quizResult->value}}%, {{$quizResult->points}} ({{$quizResult->letter}})</div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
@endsection