<?php

namespace Tests\Unit;

use App\Discipline;
use App\Profiles;
use App\QuizQuestion;
use App\Syllabus;
use Tests\TestCase;

class DisciplineTest extends TestCase
{
    public function testSector()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateQuestionIndex()
    {
        $this->markTestIncomplete();
    }

    public function testDocuments()
    {
        $this->markTestIncomplete();
    }

    public function testInSpecialityModules()
    {
        $this->markTestIncomplete();
    }

    public function testGetDisciplineListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testGetRandomId()
    {
        $this->markTestIncomplete();
    }

    public function testThemeLangs()
    {
        $this->markTestIncomplete();
    }

    public function testSave()
    {
        $this->markTestIncomplete();
    }

    public function testDisciplineFiles()
    {
        $this->markTestIncomplete();
    }

    public function testGetQuizQuestions()
    {
        $disciplineId = 2622;

        $discipline = Discipline::getById($disciplineId);

        // Test1 questions
        $questions = $discipline->getQuizQuestions(Profiles::EDUCATION_LANG_RU, 5, true);

        $this->assertIsArray($questions);
        $this->assertSame(10, count($questions));

        foreach ($questions as $question) {
            if ($question->discipline_id != $disciplineId) {
                $this->fail('Wrong discipline ID');
            }

            if (!($question instanceof QuizQuestion)) {
                $this->fail('Wrong QuizQuestion class');
            }
        }

        // Exam questions
        $questions = $discipline->getQuizQuestions(Profiles::EDUCATION_LANG_RU, 5);

        $this->assertIsArray($questions);
        $this->assertSame(20, count($questions));

        foreach ($questions as $question) {
            if ($question->discipline_id != $disciplineId) {
                $this->fail('Wrong discipline ID');
            }

            if (!($question instanceof QuizQuestion)) {
                $this->fail('Wrong QuizQuestion class');
            }
        }
    }

    public function testGetById()
    {
        $this->markTestIncomplete();
    }

    public function testGetRandomPracticeId()
    {
        $this->markTestIncomplete();
    }

    public function testHasCourseworkByUserId()
    {
        $this->markTestIncomplete();
    }

    public function testGetLanguageLevel()
    {
        $this->markTestIncomplete();
    }

    public function testGetRandomDiplomaWorkId()
    {
        $this->markTestIncomplete();
    }

    public function testGetNameAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetSpecialityExamDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testGetDependencyArray()
    {
        $this->markTestIncomplete();
    }

    public function testGetLocaleNameById()
    {
        $this->markTestIncomplete();
    }

    public function testGetRandomNotPracticeNotDiplomaWorkId()
    {
        $this->markTestIncomplete();
    }

    public function testUnresolvedDependencies()
    {
        $this->markTestIncomplete();
    }

    public function testHasCoursework()
    {
        $this->markTestIncomplete();
    }

    public function testGetArrayForSelect()
    {
        $this->markTestIncomplete();
    }

    public function testGetQuestionIdListByCorrectCount()
    {
        $this->markTestIncomplete();
    }

    public function testIsPracticeTime()
    {
        $this->markTestIncomplete();
    }

    public function testGetHasSyllabusesAttribute()
    {
        $discipline = factory(Discipline::class)->create();

        $this->assertFalse($discipline->has_syllabuses);

        $discipline->syllabuses()->save(factory(Syllabus::class)->make());

        $this->assertTrue($discipline->has_syllabuses);

        $discipline->syllabuses()->forceDelete();
        $discipline->forceDelete();
    }
}
