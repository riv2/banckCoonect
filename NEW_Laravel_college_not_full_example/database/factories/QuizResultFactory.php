<?php

use App\QuizResult;
use App\Services\LanguageService;
use App\Services\StudentRating;
use App\StudentDiscipline;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    QuizResult::class,
    function (Faker $faker) {
        $SDId = StudentDiscipline::getRandomId();

        /** @var StudentDiscipline $SD */
        $SD = StudentDiscipline::where('id', $SDId)->first();

        $type = array_random([QuizResult::TYPE_TEST1, QuizResult::TYPE_EXAM]);

        $value = rand(0, 100);

        $points = ($type == QuizResult::TYPE_TEST1) ? StudentDiscipline::getTest1ResultPoints($value) : StudentDiscipline::getExamResultPoints($value);

        return [
            'type' => $type,
            'lang' => array_random([LanguageService::LANGUAGE_RU, LanguageService::LANGUAGE_EN, LanguageService::LANGUAGE_KZ]),
            'user_id' => $SD->student_id,
            'discipline_id' => $SD->discipline_id,
            'student_discipline_id' => $SD->id,
            'hash' =>'UNIT_TEST',
            'value' => $value,
            'points' => $points,
            'letter' => StudentRating::getLetter($value),
            'blur' => array_random([true, false]),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
);

$factory->state(QuizResult::class, 'test1', function (Faker $faker) {
    $value = rand(0, 100);

    return [
        'type' => QuizResult::TYPE_TEST1,
        'value' => $value,
        'points' => StudentDiscipline::getTest1ResultPoints($value),
        'letter' => StudentRating::getLetter($value)
    ];
});

$factory->state(QuizResult::class, 'exam', function (Faker $faker) {
    $value = rand(0, 100);

    return [
        'type' => QuizResult::TYPE_EXAM,
        'value' => $value,
        'points' => StudentDiscipline::getExamResultPoints($value),
        'letter' => StudentRating::getLetter($value)
    ];
});
