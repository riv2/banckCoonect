<?php

namespace Tests\Feature;

use App\QuizAnswer;
use App\QuizQuestion;
use App\Services\Auth;
use App\Syllabus;
use App\User;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyllabusLanguageTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testSyllabus()
    {
        $this->assertTrue(true);
        /*$user = User::where('id', 160)->first();
        $user->studentProfile->education_lang = 'ru';
        $this->actingAs($user);

        $syllabus = new Syllabus();
        $syllabus->theme_number = 'theme number ru';
        $syllabus->theme_number_en = 'theme number en';
        $syllabus->theme_name = 'theme name ru';
        $syllabus->theme_name_en = 'theme name en';
        $syllabus->literature = 'theme literature ru';
        $syllabus->literature_en = 'theme literature en';

        $this->assertTrue($syllabus->theme_number == 'theme number ru');
        $this->assertTrue($syllabus->theme_name == 'theme name ru');
        $this->assertTrue($syllabus->literature == 'theme literature ru');

        Auth::user()->studentProfile->education_lang = 'en';
        $this->assertTrue($syllabus->theme_number == 'theme number en');
        $this->assertTrue($syllabus->theme_name == 'theme name en');
        $this->assertTrue($syllabus->literature == 'theme literature en');

        Auth::user()->studentProfile->education_lang = 'de';
        $this->assertTrue($syllabus->theme_number == 'theme number ru');
        $this->assertTrue($syllabus->theme_name == 'theme name ru');
        $this->assertTrue($syllabus->literature == 'theme literature ru');

        Auth::user()->studentProfile->education_lang = 'fr';
        $syllabus->theme_number = '';
        $syllabus->theme_name = '';
        $syllabus->literature = '';
        $this->assertTrue($syllabus->theme_number == 'theme number en');
        $this->assertTrue($syllabus->theme_name == 'theme name en');
        $this->assertTrue($syllabus->literature == 'theme literature en');*/
    }
/*
    public function testQuizeQuestion()
    {
        $user = User::where('id', 160)->first();
        $user->studentProfile->education_lang = 'ru';
        $this->actingAs($user);

        $question = new QuizQuestion();
        $question->question = 'ru';
        $question->question_en = 'en';

        $this->assertTrue($question->question == 'ru');

        Auth::user()->studentProfile->education_lang = 'en';
        $this->assertTrue($question->question == 'en');

        Auth::user()->studentProfile->education_lang = 'fr';
        $this->assertTrue($question->question == 'ru');

        Auth::user()->studentProfile->education_lang = 'de';
        $question->question = '';
        $this->assertTrue($question->question == 'en');
    }

    public function testQuizeAnswer()
    {
        $user = User::where('id', 160)->first();
        $user->studentProfile->education_lang = 'ru';
        $this->actingAs($user);

        $answer = new QuizAnswer();
        $answer->answer = 'ru';
        $answer->answer_en = 'en';

        $this->assertTrue($answer->answer == 'ru');

        Auth::user()->studentProfile->education_lang = 'en';
        $this->assertTrue($answer->answer == 'en');

        Auth::user()->studentProfile->education_lang = 'fr';
        $this->assertTrue($answer->answer == 'ru');

        Auth::user()->studentProfile->education_lang = 'de';
        $answer->answer = '';
        $this->assertTrue($answer->answer == 'en');
    }*/

}
