<?php

namespace Tests\Unit;

use App\QuizAnswer;
use App\QuizQuestion;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class QuizQuestionTest extends TestCase
{

    public function testFieldByLang()
    {
        $this->markTestIncomplete();
    }

    public function testGetByCorrectCount()
    {
        $this->markTestIncomplete();
    }

    public function testGetMaxPoints()
    {
        /** @var QuizQuestion $question */
        $question = factory(QuizQuestion::class)->make();
        $question->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 1]);
        $question->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 3]);

        $this->assertSame(4, $question->getMaxPoints());
    }

    public function testCreateQuestion()
    {
        $this->markTestIncomplete();
    }

    public function testEmptyFieldByLang()
    {
        $this->markTestIncomplete();
    }

    public function testGetAnswersForSnapshot()
    {
        $this->markTestIncomplete();
    }

    public function testGetMaxPointsFromArray()
    {
        /** @var QuizQuestion $question1 */
        $question1 = factory(QuizQuestion::class)->make();
        $question1->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 1]);
        $question1->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question1->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question1->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 3]);

        /** @var QuizQuestion $question2 */
        $question2 = factory(QuizQuestion::class)->make();
        $question2->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 5]);
        $question2->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question2->answers[] = factory(QuizAnswer::class)->state('wrong')->make();
        $question2->answers[] = factory(QuizAnswer::class)->state('correct')->make(['points' => 2]);

        $this->assertSame(11, QuizQuestion::getMaxPointsFromArray([$question1, $question2]));
    }

    public function testGetPointTotalAndAnswers()
    {
        $question1 = factory(QuizQuestion::class)->create();
        $question1
            ->answers()
            ->saveMany([
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('correct')->make(['points' => 1]),
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('wrong')->make(),
            ]);

        $question2 = factory(QuizQuestion::class)->create();
        $question2
            ->answers()
            ->saveMany([
                factory(QuizAnswer::class)->state('correct')->make(['points' => 3]),
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('wrong')->make(),
            ]);

        $question3 = factory(QuizQuestion::class)->create();
        $question3
            ->answers()
            ->saveMany([
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('correct')->make(['points' => 1]),
                factory(QuizAnswer::class)->state('correct')->make(['points' => 4]),
            ]);

        $question4 = factory(QuizQuestion::class)->create();
        $question4
            ->answers()
            ->saveMany([
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('wrong')->make(),
                factory(QuizAnswer::class)->state('correct')->make(['points' => 2]),
                factory(QuizAnswer::class)->state('wrong')->make(),
            ]);

        // All correct
        $userAnswers = [
            [
                'id' => $question1->id,
                'answer' => $question1->answers[1]->id,
            ],
            [
                'id' => $question2->id,
                'answer' => $question2->answers[0]->id,
            ],
            [
                'id' => $question3->id,
                'answer' => [$question3->answers[2]->id, $question3->answers[3]->id],
            ],
            [
                'id' => $question4->id,
                'answer' => $question4->answers[2]->id,
            ]
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers);
        $this->assertIsArray($data);
        $this->assertCount(3, $data);
        $this->assertIsInt($data[0]);
        $this->assertIsArray($data[1]);
        $this->assertIsInt($data[2]);
        $this->assertLessThanOrEqual(100, $data[2]);

        $this->assertSame(11, $data[0]);
        $this->assertCount(5, $data[1]);
        $this->assertArrayHasKey('question_id', $data[1][0]);
        $this->assertArrayHasKey('answer_id', $data[1][1]);
        $this->assertSame(100, $data[2]);

        // First wrong
        $userAnswers1stWrong = $userAnswers;
        $userAnswers1stWrong[0] = [
            'id' => $question1->id,
            'answer' => $question1->answers[0]->id
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers1stWrong);
        $this->assertSame(10, $data[0]);
        $this->assertCount(5, $data[1]);
        $this->assertSame(100, $data[2]);

        // Second empty
        $userAnswers2ndEmpty = $userAnswers;
        $userAnswers2ndEmpty[1] = [
            'id' => $question2->id,
            'answer' => ''
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers2ndEmpty);
        $this->assertSame(8, $data[0]);
        $this->assertCount(5, $data[1]);
        $this->assertArrayHasKey('answer_id', $data[1][1]);
        $this->assertNull($data[1][1]['answer_id']);
        $this->assertSame(75, $data[2]);

        // Third wrong partially
        $userAnswers3stWrongPartially = $userAnswers;
        $userAnswers3stWrongPartially[2] = [
            'id' => $question3->id,
            'answer' => [$question3->answers[2]->id, $question3->answers[0]->id]
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers3stWrongPartially);
        $this->assertSame(7, $data[0]);
        $this->assertCount(5, $data[1]);
        $this->assertSame(100, $data[2]);

        // Third wrong partially
        $userAnswers3stWrongFully = $userAnswers;
        $userAnswers3stWrongFully[2] = [
            'id' => $question3->id,
            'answer' => [$question3->answers[0]->id, $question3->answers[1]->id]
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers3stWrongFully);
        $this->assertSame(6, $data[0]);
        $this->assertCount(5, $data[1]);
        $this->assertSame(100, $data[2]);

        // Third empty
        $userAnswers3stEmpty = $userAnswers;
        $userAnswers3stEmpty[2] = [
            'id' => $question3->id,
            'answer' => ''
        ];

        $data = QuizQuestion::getPointTotalAndAnswers($userAnswers3stEmpty);
        $this->assertSame(6, $data[0]);
        $this->assertCount(4, $data[1]);
        $this->assertArrayHasKey('answer_id', $data[1][2]);
        $this->assertNull($data[1][2]['answer_id']);
        $this->assertSame(75, $data[2]);

        $question1->answers()->delete();
        $question1->delete();
        $question2->answers()->delete();
        $question2->delete();
        $question3->answers()->delete();
        $question3->delete();
        $question4->answers()->delete();
        $question4->delete();
    }

    public function testGetSyllabusAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testAttachAudio()
    {
        $this->markTestIncomplete();
    }

    public function testHasAudio()
    {
        $questions[] = factory(QuizQuestion::class)->make();
        $questions[] = factory(QuizQuestion::class)->make();
        $questions[] = factory(QuizQuestion::class)->make();

        $questions[0]->audiofiles = [];
        $questions[1]->audiofiles = [];
        $questions[2]->audiofiles = [];

        $this->assertFalse(QuizQuestion::hasAudio($questions));

        $questions[1]->audiofiles = [1];

        $this->assertTrue(QuizQuestion::hasAudio($questions));
    }

    public function testUpdateQuestion()
    {
        $this->markTestIncomplete();
    }

    public function testGetCorrectAnswersCountAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetQuestionTextOnlyAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetTotalPoints()
    {
        $this->markTestIncomplete();
    }

    public function testGetWithAnswers()
    {
        $question = factory(QuizQuestion::class)->create();
        $question
            ->answers()
            ->saveMany([
                factory(QuizAnswer::class)->make(),
                factory(QuizAnswer::class)->make(),
                factory(QuizAnswer::class)->make(),
                factory(QuizAnswer::class)->make(),
            ]);

        $questionGot = QuizQuestion::getWithAnswers($question->id);

        $this->assertInstanceOf(QuizQuestion::class, $questionGot);
        $this->assertNotEmpty($questionGot->answers);
        $this->assertCount(4, $questionGot->answers);

        foreach ($questionGot->answers as $answer) {
            if (!($answer instanceof QuizAnswer)) {
                $this->fail('Wrong QuizAnswer class');
            }
        }

        $question->answers()->delete();
        $question->delete();
    }

    public function testSyncAnswers()
    {
        $this->markTestIncomplete();
    }

    public function testGetActualQuestionAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetCorrectAnswersCount()
    {
        $this->markTestIncomplete();
    }

    public function testGetHasMultiAnswerAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testAttachAudioJson()
    {
        $this->markTestIncomplete();
    }

    public function testGetDefaultLanguage()
    {
        $this->markTestIncomplete();
    }

    public function testDeleteWithAnswers()
    {
        $this->markTestIncomplete();
    }
}
