<?php

use App\SyllabusTask;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;
use App\Syllabus;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(
    SyllabusTask::class,
    function (Faker $faker) {
        /** @var Syllabus $syllabus */
        $syllabus = Syllabus::where('id', \App\Syllabus::getRandomId())->first();

        return [
            'syllabus_id' => $syllabus->id,
            'discipline_id' => $syllabus->discipline_id,
            'language' => $syllabus->language,
            'type' => array_random([
                SyllabusTask::TYPE_TEXT,
                SyllabusTask::TYPE_IMAGE,
                SyllabusTask::TYPE_LINK ,
                SyllabusTask::TYPE_AUDIO,
                SyllabusTask::TYPE_VIDEO,
                SyllabusTask::TYPE_EVENT,
                SyllabusTask::TYPE_ESSAY
            ]),
            'points' => rand(1, 40),
            'event_date' => null,
            'event_place' => null,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
            'text_data' => 'UNIT_TEST' . $faker->text,
            'img_data' => null,
            'link_data' => null,
            'audio_data' => null,
            'video_data' => null
        ];
    }
);
