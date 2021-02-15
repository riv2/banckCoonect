<?php

namespace Tests\Unit;

use App\Discipline;
use App\QuizAnswer;
use App\QuizQuestion;
use App\QuizResult;

//use PHPUnit\Framework\TestCase;
use App\Services\LanguageService;
use App\Services\StudentRating;
use App\StudentDiscipline;
use App\User;
use Tests\TestCase;

class QuizResultTest extends TestCase
{

    public function testGetTypeTextAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetLastTest1()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $lastQR = QuizResult::getLastTest1($SDId);

        $this->assertSame($QR3->id, $lastQR->id);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testGetBestExam()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $bestQR = QuizResult::getBestExam($SDId);

        $this->assertSame($QR2->id, $bestQR->id);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testGetTypesArray()
    {
        $this->markTestIncomplete();
    }

    public function testAddValue()
    {
        $this->markTestIncomplete();
    }

    public function testGetListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testGetLastExam()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $lastQR = QuizResult::getLastExam($SDId);

        $this->assertSame($QR3->id, $lastQR->id);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testTest1AttemptsCount()
    {
        $this->markTestIncomplete();
    }

    public function testGetBestTest1()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $bestQR = QuizResult::getBestTest1($SDId);

        $this->assertSame($QR2->id, $bestQR->id);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testGenerateHash()
    {
        $this->assertSame(10, strlen(QuizResult::generateHash()));

        $this->assertNotEquals(QuizResult::generateHash(), QuizResult::generateHash());
    }

    public function testAddSnapshot()
    {
        $this->markTestIncomplete();
    }

    public function testExamAttemptsCount()
    {
        $this->markTestIncomplete();
    }

    public function testHoursFromLastResult()
    {
        $this->markTestIncomplete();
    }

    public function testExistsByHash()
    {
        $this->markTestIncomplete();
    }

    public function testSetResultAnswers()
    {
        $count = 3;

        $quizResult = factory(QuizResult::class)->create();

        $answers = [];
        for ($i = 0; $i < $count; $i++) {
            $question = QuizQuestion::getRandom();
            $answers[] = [
                'question_id' => $question->id,
                'answer_id' => QuizAnswer::getRandomId($question->id)
            ];
        }

        $quizResult->setResultAnswers($answers);

        $this->assertCount($count, $quizResult->answers);

        $quizResult->answers()->delete();
        $quizResult->forceDelete();
    }

    public function testAddExam()
    {
        /** @var QuizResult $standartQR */
        $standartQR = factory(QuizResult::class)->state('exam')->make();

        $quizResult = QuizResult::addExam(
            $standartQR->user_id,
            $standartQR->lang,
            $standartQR->discipline_id,
            $standartQR->student_discipline_id,
            $standartQR->hash,
            $standartQR->value,
            $standartQR->blur
        );

        $this->assertNotNull($quizResult);

        $quizResultDouble = QuizResult::addExam(
            $standartQR->user_id,
            $standartQR->lang,
            $standartQR->discipline_id,
            $standartQR->student_discipline_id,
            $standartQR->hash,
            $standartQR->value,
            $standartQR->blur
        );

        $this->assertNull($quizResultDouble);

        /** @var QuizResult $QR */
        $QR = QuizResult::where('id', $quizResult->id)->first();

        $this->assertNotEmpty($QR);
        $this->assertSame(QuizResult::TYPE_EXAM, $QR->type);
        $this->assertSame($standartQR->lang, $QR->lang);
        $this->assertSame($standartQR->user_id, $QR->user_id);
        $this->assertSame($standartQR->discipline_id, $QR->discipline_id);
        $this->assertSame($standartQR->student_discipline_id, $QR->student_discipline_id);
        $this->assertSame($standartQR->hash, $QR->hash);
        $this->assertSame($standartQR->value, $QR->value);
        $this->assertSame($standartQR->points, $QR->points);
        $this->assertSame($standartQR->letter, $QR->letter);
        $this->assertSame($standartQR->blur, $QR->blur);

        $QR->forceDelete();
    }

    public function testAddTest1()
    {
        /** @var QuizResult $standartQR */
        $standartQR = factory(QuizResult::class)->state('test1')->make();

        $quizResult = QuizResult::addTest1(
            $standartQR->user_id,
            $standartQR->lang,
            $standartQR->discipline_id,
            $standartQR->student_discipline_id,
            $standartQR->hash,
            $standartQR->value,
            $standartQR->blur
        );

        $this->assertNotNull($quizResult);

        $quizResultDouble = QuizResult::addTest1(
            $standartQR->user_id,
            $standartQR->lang,
            $standartQR->discipline_id,
            $standartQR->student_discipline_id,
            $standartQR->hash,
            $standartQR->value,
            $standartQR->blur
        );

        $this->assertNull($quizResultDouble);

        /** @var QuizResult $QR */
        $QR = QuizResult::where('id', $quizResult->id)->first();

        $this->assertNotEmpty($QR);
        $this->assertSame(QuizResult::TYPE_TEST1, $QR->type);
        $this->assertSame($standartQR->lang, $QR->lang);
        $this->assertSame($standartQR->user_id, $QR->user_id);
        $this->assertSame($standartQR->discipline_id, $QR->discipline_id);
        $this->assertSame($standartQR->student_discipline_id, $QR->student_discipline_id);
        $this->assertSame($standartQR->hash, $QR->hash);
        $this->assertSame($standartQR->value, $QR->value);
        $this->assertSame($standartQR->points, $QR->points);
        $this->assertSame($standartQR->letter, $QR->letter);
        $this->assertSame($standartQR->blur, $QR->blur);

        $QR->forceDelete();
    }
}
