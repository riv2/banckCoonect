<?php

use App\Services\StudentRating;
use App\StudentDiscipline;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    StudentDiscipline::class,
    function (Faker $faker) {
        return [
            'discipline_id' => \App\Discipline::getRandomId(),
            'test1_blur' => false,
            'test1_zeroed_by_time' => false,
            'test_manual' => false,
            'test_result_trial' => false,
            'test_blur' => false,
            'exam_zeroed_by_time' => false,
            'test_qr_checked' => false,
            'final_manual' => false,
            'payed' => false,
            'free_credits' => 0,
            'remote_access' => false,
            'corona_distant' => 0,
            'iteration' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'migrated' => 0,
            'is_elective' => false,
            'task_manual' => false,
            'task_blur' => false,
            'sro_zeroed_by_time' => false,
        ];
    }
);

$factory->state(StudentDiscipline::class, 'practice', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomPracticeId()
    ];
});

$factory->state(StudentDiscipline::class, 'diploma_work', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomDiplomaWorkId()
    ];
});

$factory->state(StudentDiscipline::class, 'not_practice_not_diploma_work', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomNotPracticeNotDiplomaWorkId()
    ];
});

$factory->state(StudentDiscipline::class, '1credit', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomIdNCredit(1)
    ];
});

$factory->state(StudentDiscipline::class, '2credit', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomIdNCredit(2)
    ];
});

$factory->state(StudentDiscipline::class, '3credit', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomIdNCredit(3)
    ];
});

$factory->state(StudentDiscipline::class, '4credit', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomIdNCredit(4)
    ];
});

$factory->state(StudentDiscipline::class, '5credit', function() {
    return [
        'discipline_id' => \App\Discipline::getRandomIdNcredit(5)
    ];
});

$factory->state(StudentDiscipline::class, 'finished', function() {
    $result = rand(0, 100);
    $disciplineCredits = rand(1, 10);

    return [
        'final_result' => $result,
        'final_result_points' => StudentRating::getFinalResultPoints($result),
        'final_result_gpa' => StudentRating::getDisciplineGpa($result, $disciplineCredits),
        'final_result_letter' => StudentRating::getLetter($result),
        'final_date' => Carbon::now(),
        'final_manual' => false
    ];
});
