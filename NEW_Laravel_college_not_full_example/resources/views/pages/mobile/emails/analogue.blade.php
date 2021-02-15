<?php
/**
 * @var \App\User $user
 * @var \App\StudentDiscipline $studentDiscipline
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>Студент {{$user->studentProfile->fio}} учащийся на специальности "{{$user->studentProfile->speciality->name}}" запросил перезачет дисциплины "{{$studentDiscipline->discipline->name}}"</div>

@if (!empty($studentDiscipline->notes))
    <div>Пояснения:</div>
    <div>{{$studentDiscipline->notes}}</div>
@endif

</body>
</html>