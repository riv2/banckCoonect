<?php

namespace Tests\Unit;

use App\BcApplications;
use App\Discipline;
use App\Profiles;
use App\QuizResult;
use App\Semester;
use App\SpecialitySemester;
use App\StudentDiscipline;

use App\StudentGpa;
use App\Syllabus;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class StudentDisciplineTest extends TestCase
{

    public function testSetExamResultManual()
    {
        $this->markTestIncomplete();
    }

    public function testHasSubmoduleDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testSetTest1ButtonShow()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user
            ->studentProfile()
            ->save(
                factory(Profiles::class)->states('active', 'fulltime')->make()
            );
        $user
            ->bcApplication()
            ->save(
                factory(BcApplications::class)->state(BcApplications::EDUCATION_HIGH_SCHOOL)->make()
            );

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->make([
            'student_id' => $user->id,
            'plan_semester' => $semesterString
        ]);

        $test1Semester = new Semester();
        $test1Semester->type = Semester::TYPE_TEST1;
        $test1Semester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $test1Semester->start_date = Carbon::today();
        $test1Semester->end_date = Carbon::tomorrow();
        $test1Semester->number = $semesterNumber;
        $test1Semester->save();

        $test1RetakeSemester = new Semester();
        $test1RetakeSemester->type = Semester::TYPE_TEST1_RETAKE;
        $test1RetakeSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $test1RetakeSemester->start_date = Carbon::today();
        $test1RetakeSemester->end_date = Carbon::tomorrow();
        $test1RetakeSemester->number = $semesterNumber;
        $test1RetakeSemester->save();

        // Check
        $SD->setTest1ButtonShow($user);
        $this->assertTrue($SD->test1ButtonShow);

        // Not Test1Time & Test1Retake
        $user->semesterDatesFlush();
        $test1Semester->start_date = Carbon::tomorrow();
        $test1Semester->save();
        $SD->setTest1ButtonShow($user);
        $this->assertTrue($SD->test1ButtonShow);

        // Not Test1Time & not Test1Retake
        $user->semesterDatesFlush();
        $test1RetakeSemester->start_date = Carbon::tomorrow();
        $test1RetakeSemester->save();
        $SD->setTest1ButtonShow($user);
        $this->assertFalse($SD->test1ButtonShow);

        // Test1Time & not Test1Retake
        $user->semesterDatesFlush();
        $test1Semester->start_date = Carbon::today();
        $test1Semester->save();
        $SD->setTest1ButtonShow($user);
        $this->assertTrue($SD->test1ButtonShow);

        // Not Fulltime
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL;
        $SD->setTest1ButtonShow($user);
        $this->assertFalse($SD->test1ButtonShow);
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $SD->setTest1ButtonShow($user);
        $this->assertTrue($SD->test1ButtonShow);

        // Practice

        $SD->discipline->is_practice = true;
        $SD->setTest1ButtonShow($user);
        $this->assertFalse($SD->test1ButtonShow);
        $SD->discipline->is_practice = false;
        $SD->setTest1ButtonShow($user);
        $this->assertTrue($SD->test1ButtonShow);

        // Diploma
        $SD->discipline->has_diplomawork = true;
        $SD->setTest1ButtonShow($user);
        $this->assertFalse($SD->test1ButtonShow);

        $user->forceDelete();
        $test1Semester->delete();
        $test1RetakeSemester->delete();
    }

    public function testHasTest1Attempt()
    {
        $this->markTestIncomplete();
    }

    public function testClearPlanSemester()
    {
        $this->markTestIncomplete();
    }

    public function testGetResultsCounts()
    {
        $this->markTestIncomplete();
    }

    public function testGetAmount()
    {
        $this->markTestIncomplete();
    }

    public function testHasTest1FreeAttempt()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        factory(QuizResult::class)->state('test1')->create([
            'user_id' => $user->id,
            'student_discipline_id' => $SD->id
        ]);

        $this->assertGreaterThan(1, StudentDiscipline::TEST1_FREE_ATTEMPTS);
        $this->assertTrue($SD->hasTest1FreeAttempt());

        for ($i = 1; $i < StudentDiscipline::TEST1_FREE_ATTEMPTS; $i++) {
            factory(QuizResult::class)->state('test1')->create([
                'user_id' => $user->id,
                'student_discipline_id' => $SD->id
            ]);
        }

        $this->assertFalse($SD->hasTest1FreeAttempt());

        $SD->quizeResults()->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testSetPlanSemester()
    {
        $this->markTestIncomplete();
    }

    public function testGetSROResultPoints()
    {
        $points = StudentDiscipline::getSROResultPoints(100);
        $this->assertIsInt($points);
        $this->assertSame(StudentDiscipline::SRO_MAX_POINTS, $points);
        $this->assertSame(StudentDiscipline::SRO_MAX_POINTS / 2, StudentDiscipline::getSROResultPoints(50));
    }

    public function testGetDisciplineForPay()
    {
        $this->markTestIncomplete();
    }

    public function testCombineAndSortSubmodulesAndDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testSetDisciplineToStudents()
    {
        $this->markTestIncomplete();
    }

    public function testSetExamTrial()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['5credit'])->create(['student_id' => $user->id]);

        $SD->setExamTrial();

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertTrue($SDFromDB->test_result_trial);
        $this->assertFalse($SDFromDB->test_qr_checked);

        $SD->delete();
        $user->forceDelete();
    }

    public function testGetRecommended()
    {
        $this->markTestIncomplete();
    }

    public function testGetBoughtDisciplinesCredits()
    {
        $this->markTestIncomplete();
    }

    public function testGetBySemester()
    {
        $this->markTestIncomplete();
    }

    public function testGetTest1ResultPoints()
    {
        $points = StudentDiscipline::getTest1ResultPoints(100);
        $this->assertIsInt($points);
        $this->assertSame(StudentDiscipline::TEST1_MAX_POINTS, $points);
        $this->assertSame(StudentDiscipline::TEST1_MAX_POINTS / 2, StudentDiscipline::getTest1ResultPoints(50));
    }

    public function testGetDependenciesAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreditsCountForBuy()
    {
        $this->markTestIncomplete();
    }

    public function testSetTest1AppealShow()
    {
        $this->markTestIncomplete();
    }

    public function testGetNotConfirmed()
    {
        $this->markTestIncomplete();
    }

    public function testHasExamFreeAttemptCorona()
    {
        $this->markTestIncomplete();
    }

    public function testGetFinishedDisciplinesCreditsSum()
    {
        $user = factory(User::class)->create();

        $user->studentDisciplines()->saveMany(
            [
                factory(StudentDiscipline::class)->states(['1credit', 'finished'])->make(),
                factory(StudentDiscipline::class)->states(['2credit', 'finished'])->make(),
                factory(StudentDiscipline::class)->states(['5credit', 'finished'])->make(),
            ]
        );

        $this->assertSame(8, StudentDiscipline::getFinishedDisciplinesCreditsSum($user->id));

        $user->studentDisciplines()->delete();

        $this->assertSame(0, StudentDiscipline::getFinishedDisciplinesCreditsSum($user->id));

        $user->forceDelete();
    }

    public function testIsExamPaidAttemptCorona()
    {
        $this->markTestIncomplete();
    }

    public function testCalculateFinalResult()
    {
        $user = factory(User::class)->create();
        $user->studentProfile()->save(factory(Profiles::class)->states('active', 'fulltime')->make());

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['5credit'])->create(['student_id' => $user->id]);

        $SD->test1_result_points = StudentDiscipline::TEST1_MAX_POINTS;
        $SD->test_result_points = StudentDiscipline::EXAM_MAX_POINTS;
        $SD->task_result_points = StudentDiscipline::SRO_MAX_POINTS;

        $SD->calculateFinalResult();

        $this->assertSame(100, $SD->final_result);

        // Distance learning
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $user->studentProfile->save();

        /** @var StudentDiscipline $SD2 */
        $SD2 = factory(StudentDiscipline::class)->states(['5credit'])->create(['student_id' => $user->id]);
        $SD2->test_result = rand(0, 100);
        $SD2->calculateFinalResult();

        $result = $SD2->test1_result_points + $SD2->task_result_points + $SD2->test_result_points;
        $this->assertSame($result, $SD2->final_result);

        $SD2->calculateFinalResult();

        $SD->delete();
        $SD2->delete();
        $user->studentProfile->delete();
        $user->forceDelete();
    }

    public function testGetWithoutExam()
    {
        $this->markTestIncomplete();
    }

    public function testSetRemoteAccessBuyAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testSetExamButtonEnabled()
    {
        $this->markTestIncomplete();
    }

    public function testSetPayButtonShow()
    {
        $this->markTestIncomplete();
    }

    public function testGetTest1AppealAvailableAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetOne()
    {
        $SD = StudentDiscipline::first();

        $SD1 = StudentDiscipline::getOne($SD->student_id, $SD->discipline_id);

        $this->assertSame($SD->id, $SD1->id);
    }

    public function testGetForPlanEdit()
    {
        $this->markTestIncomplete();
    }

    public function testStudentConfirmPlanSemester()
    {
        $this->markTestIncomplete();
    }

    public function testHasExamFreeAttempt()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        factory(QuizResult::class)->state('exam')->create([
            'user_id' => $user->id,
            'student_discipline_id' => $SD->id
        ]);

        $this->assertGreaterThan(1, StudentDiscipline::EXAM_FREE_ATTEMPTS);
        $this->assertTrue($SD->hasExamFreeAttempt());

        for ($i = 1; $i < StudentDiscipline::EXAM_FREE_ATTEMPTS; $i++) {
            factory(QuizResult::class)->state('exam')->create([
                'user_id' => $user->id,
                'student_discipline_id' => $SD->id
            ]);
        }

        $this->assertFalse($SD->hasExamFreeAttempt());

        $SD->quizeResults()->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testSetExamResult()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('exam')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $SD->setExamResult();

        $this->assertSame($SD->test_result, $QR2->value);
        $this->assertSame($SD->test_result_points, $QR2->points);
        $this->assertSame($SD->test_result_letter, $QR2->letter);
        $this->assertSame($SD->test_blur, $QR2->blur);
        $this->assertFalse($SD->test_manual);
        $this->assertFalse($SD->exam_zeroed_by_time);
        $this->assertFalse($SD->test_result_trial);
        $this->assertFalse($SD->test_qr_checked);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testCreditsByPlanSemesters()
    {
        $this->markTestIncomplete();
    }

    public function testSetTest1ZeroByTime()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        $SD->setTest1ZeroByTime();

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertSame(0, $SDFromDB->test1_result);
        $this->assertSame(0, $SDFromDB->test1_result_points);
        $this->assertSame('F', $SDFromDB->test1_result_letter);
        $this->assertTrue($SDFromDB->test1_zeroed_by_time);

        $SD->delete();
        $user->forceDelete();
    }

    public function testExistsByUserAndDiscipline()
    {
        $this->markTestIncomplete();
    }

    public function testGetWithoutSRO()
    {
        $this->markTestIncomplete();
    }

    public function testSetTest1ButtonEnabled()
    {
        $this->markTestIncomplete();
    }

    public function testGetTest1AvailableAttribute()
    {
        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make([
            'payed' => false,
            'payed_credits' => 0
        ]);

        $this->assertFalse($SD->test1_available);

        // Payed
        $SD->payed = true;
        $this->assertTrue($SD->test1_available);

        // payed_credits > 0
        $SD->payed = false;
        $SD->payed_credits = 1;
        $this->assertTrue($SD->test1_available);
    }

    public function testExplodeSemester()
    {
        $this->markTestIncomplete();
    }

    public function testSetExamAppealShow()
    {
        $this->markTestIncomplete();
    }

    public function testGetListForStudyPage()
    {
        $this->markTestIncomplete();
    }

    public function testAllowExamInClassroom()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();
        $this->assertFalse($SDFromDB->test_qr_checked);

        StudentDiscipline::allowExamInClassroom($SD->student_id, $SD->discipline_id);

        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();
        $this->assertTrue($SDFromDB->test_qr_checked);

        $SD->delete();
        $user->forceDelete();
    }

    public function testIsTest1PaidAttempt()
    {
        $this->markTestIncomplete();
    }

    public function testGetExamAvailableAttribute()
    {
        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make([
            'payed' => false,
            'payed_credits' => 0
        ]);

        $this->assertNotNull(User::RECOVERY_EXCEPTION_USER_LIST);
        $SD->student_id = array_random(User::RECOVERY_EXCEPTION_USER_LIST);
        $this->assertFalse($SD->exam_available);

        $this->assertNotNull($SD->examDisabledFor);
        $SD->student_id = array_random($SD->examDisabledFor);
        $this->assertFalse($SD->exam_available);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->state('fulltime')->make(['course' => 2])
        );
        $user->bcApplication()->save(
            factory(BcApplications::class)->make()
        );

        $SD->student_id = $user->id;
        $SD->test1_result = 100;
        $SD->task_result = 100;
        $this->assertTrue($SD->exam_available);

        $SD->test1_result = 0;
        $this->assertTrue($SD->exam_available);

        $SD->test1_result = null;
        $this->assertFalse($SD->exam_available);

        $SD->test1_result = 100;
        $SD->task_result = null;
        $this->assertFalse($SD->exam_available);

        $SD->task_result = 0;
        $this->assertTrue($SD->exam_available);

        // Distance learning
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $user->studentProfile->save();
        $SD->user = $user;

        $this->assertTrue($SD->user->distance_learning);

        $SD->payed = true;
        $this->assertTrue($SD->exam_available);

        $SD->payed = false;
        $this->assertFalse($SD->exam_available);

        // Not distance
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $user->studentProfile->course = 1;
        $SD->user = $user;

        $SD->test1_result = 100;
        $SD->task_result = 100;
        $SD->payed = true;
        $this->assertTrue($SD->exam_available);

        $SD->test1_result = 0;
        $SD->task_result = 0;
        $this->assertTrue($SD->exam_available);

        $SD->payed = false;
        $this->assertFalse($SD->exam_available);

        $SD->test1_result = null;
        $SD->task_result = 100;
        $SD->payed = true;
        $this->assertFalse($SD->exam_available);

        $SD->test1_result = 100;
        $SD->task_result = null;
        $SD->payed = true;
        $this->assertFalse($SD->exam_available);

        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();
    }

    public function testResultClean()
    {
        $this->markTestIncomplete();
    }

    public function testSetChooseAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testSetSROZeroByTime()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        $SD->setSroZeroByTime();

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertSame(0, $SDFromDB->task_result);
        $this->assertSame(0, $SDFromDB->task_result_points);
        $this->assertSame('F', $SDFromDB->task_result_letter);
        $this->assertTrue($SDFromDB->sro_zeroed_by_time);

        $SD->delete();
        $user->forceDelete();
    }

    public function testAdd()
    {
        $this->markTestIncomplete();
    }

    public function testSetManualResultAccess()
    {
        $this->markTestIncomplete();
    }

    public function testSetSROResult()
    {
        $this->markTestIncomplete();
    }

    public function testGetArrayForExamSheet()
    {
        $this->markTestIncomplete();
    }

    public function testChangePlanSemester()
    {
        $this->markTestIncomplete();
    }

    public function testAdminConfirmPlanSemester()
    {
        $this->markTestIncomplete();
    }

    public function testSetSROButtonShow()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user
            ->studentProfile()
            ->save(
                factory(Profiles::class)->states('active', 'fulltime')->make()
            );
        $user
            ->bcApplication()
            ->save(
                factory(BcApplications::class)->state(BcApplications::EDUCATION_HIGH_SCHOOL)->make()
            );

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->make([
            'student_id' => $user->id,
            'plan_semester' => $semesterString
        ]);

        $examSemester = new Semester();
        $examSemester->type = Semester::TYPE_SRO;
        $examSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $examSemester->start_date = Carbon::today();
        $examSemester->end_date = Carbon::tomorrow();
        $examSemester->number = $semesterNumber;
        $examSemester->save();

        $examRetakeSemester = new Semester();
        $examRetakeSemester->type = Semester::TYPE_SRO_RETAKE;
        $examRetakeSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $examRetakeSemester->start_date = Carbon::today();
        $examRetakeSemester->end_date = Carbon::tomorrow();
        $examRetakeSemester->number = $semesterNumber;
        $examRetakeSemester->save();

        // Check
        $SD->setSROButtonShow($user);
        $this->assertTrue($SD->SROButtonShow);

        // Not ExamTime & ExamRetake
        $user->semesterDatesFlush();
        $examSemester->start_date = Carbon::tomorrow();
        $examSemester->save();
        $SD->setSROButtonShow($user);
        $this->assertTrue($SD->SROButtonShow);

        // Not ExamTime & not ExamRetake
        $user->semesterDatesFlush();
        $examRetakeSemester->start_date = Carbon::tomorrow();
        $examRetakeSemester->save();
        $SD->setSROButtonShow($user);
        $this->assertFalse($SD->SROButtonShow);

        // ExamTime & not ExamRetake
        $user->semesterDatesFlush();
        $examSemester->start_date = Carbon::today();
        $examSemester->save();
        $SD->setSROButtonShow($user);
        $this->assertTrue($SD->SROButtonShow);

        // Practice

        $SD->discipline->is_practice = true;
        $SD->setSROButtonShow($user);
        $this->assertFalse($SD->SROButtonShow);
        $SD->discipline->is_practice = false;
        $SD->setSROButtonShow($user);
        $this->assertTrue($SD->SROButtonShow);

        // Diploma
        $SD->discipline->has_diplomawork = true;
        $SD->setSROButtonShow($user);
        $this->assertFalse($SD->SROButtonShow);

        $user->forceDelete();
        $examSemester->delete();
        $examRetakeSemester->delete();
    }

    public function testGetLanguageType()
    {
        $this->markTestIncomplete();
    }

    public function testSetExamZeroByTime()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        $SD->setExamZeroByTime();

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertSame(0, $SDFromDB->test_result);
        $this->assertSame(0, $SDFromDB->test_result_points);
        $this->assertSame('F', $SDFromDB->test_result_letter);
        $this->assertTrue($SDFromDB->exam_zeroed_by_time);

        $SD->delete();
        $user->forceDelete();
    }

    public function testGetDisciplineIds()
    {
        $this->markTestIncomplete();
    }

    public function testGetTestLanguage()
    {
        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make();

        /** @var Profiles $profile */
        $profile = factory(Profiles::class)->make();

        $SD->discipline->tests_lang_invert = true;
        $this->assertSame($profile->second_language, $SD->getTestLanguage($profile));

        $SD->discipline->tests_lang_invert = false;
        $this->assertSame($profile->native_language, $SD->getTestLanguage($profile));
    }

    public function testGetExamAttemptsCountAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testAddElectiveDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testMakePlan()
    {
        $this->markTestIncomplete();
    }

    public function testGetIsInheritedAttribute()
    {
        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make();

        $SD->final_result = 100;
        $SD->at_semester = null;

        $this->assertTrue($SD->is_inherited);

        $SD->final_result = 0;
        $this->assertTrue($SD->is_inherited);

        $SD->final_result = null;
        $this->assertFalse($SD->is_inherited);

        $SD->final_result = 100;
        $SD->at_semester = 1;
        $this->assertFalse($SD->is_inherited);
    }

    public function testSetTest1Result()
    {
        $user = factory(User::class)->create();
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);
        $SDId = $SD->id;

        $QR1 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 80]);
        $QR2 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 100]);
        $QR3 = factory(QuizResult::class)->state('test1')->create(['student_discipline_id' => $SDId, 'value' => 50]);

        $SD->setTest1Result();

        $this->assertSame($SD->test1_result, $QR2->value);
        $this->assertSame($SD->test1_result_points, $QR2->points);
        $this->assertSame($SD->test1_result_letter, $QR2->letter);
        $this->assertSame($SD->test1_blur, $QR2->blur);
        $this->assertFalse($SD->test1_zeroed_by_time);
        $this->assertFalse($SD->test1_result_trial);
        $this->assertFalse($SD->test1_qr_checked);

        $QR1->forceDelete();
        $QR2->forceDelete();
        $QR3->forceDelete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testCheckSubmodulesAndDisciplinesForAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testGetForPay()
    {
        $this->markTestIncomplete();
    }

    public function testGetMissed()
    {
        $this->markTestIncomplete();
    }

    public function testGetDataForStudyPage()
    {
        $this->markTestIncomplete();
    }

    public function testSetRemoteAccess()
    {
        $this->markTestIncomplete();
    }

    public function testHasAccessToSyllabuses()
    {
        $this->markTestIncomplete();
    }

    public function testSetFinalResult()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['5credit'])->create(['student_id' => $user->id]);

        $SD->setFinalResult(100);

        /** @var StudentDiscipline $SDFromDB */
        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertNotEmpty($SDFromDB);
        $this->assertSame($SD->id, $SDFromDB->id);
        $this->assertSame(100, $SDFromDB->final_result);
        $this->assertSame(4.0, $SDFromDB->final_result_points);
        $this->assertSame(20.0, $SDFromDB->final_result_gpa);
        $this->assertSame('A', $SDFromDB->final_result_letter);
        $this->assertSame($SD->final_date->timestamp, $SDFromDB->final_date->timestamp);
        $this->assertFalse($SDFromDB->final_manual);

        /** @var StudentGpa $gpa */
        $gpa = StudentGpa::where('user_id', $user->id)->first();

        $this->assertSame(4.0, $gpa->value);

        $gpa->delete();
        $SD->delete();
        $user->forceDelete();
    }

    public function testGetExamResultPoints()
    {
        $points = StudentDiscipline::getExamResultPoints(100);
        $this->assertIsInt($points);
        $this->assertSame(StudentDiscipline::EXAM_MAX_POINTS, $points);
        $this->assertSame(StudentDiscipline::EXAM_MAX_POINTS / 2, StudentDiscipline::getExamResultPoints(50));
    }

    public function testGetByStudentIdsAndDisciplineId()
    {
        $this->markTestIncomplete();
    }

    public function testSetTestResult()
    {
        $this->markTestIncomplete();
    }

    public function testIsPlannedToSemester()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreditsForPartialBuy()
    {
        $this->markTestIncomplete();
    }

    public function testPlannedDisciplinesCredits()
    {
        $this->markTestIncomplete();
    }

    public function testGetExamAppealAvailableAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetMigratedType()
    {
        $this->markTestIncomplete();
    }

    public function testGetPlanConfirmedAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetBoughtCredits()
    {
        $this->markTestIncomplete();
    }

    public function testSetPayProcessing()
    {
        $this->markTestIncomplete();
    }

    public function testSetBuyAvailable()
    {
        $this->markTestIncomplete();
    }

    public function testAllowTest1InClassroom()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->create(['student_id' => $user->id]);

        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();
        $this->assertNull($SDFromDB->test1_qr_checked);

        StudentDiscipline::allowTest1InClassroom($SD->student_id, $SD->discipline_id);

        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();
        $this->assertTrue($SDFromDB->test1_qr_checked);

        $SD->delete();
        $user->forceDelete();
    }

    public function testGetStudentsIdsByDisciplinesIds()
    {
        $this->markTestIncomplete();
    }

    public function testSetSROResultManual()
    {
        $this->markTestIncomplete();
    }

    public function testGetOneOrderByPayedCredits()
    {
        $this->markTestIncomplete();
    }

    public function testIsExamPaidAttempt()
    {
        $this->markTestIncomplete();
    }

    public function testGetMigratedTypeAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetSroAvailableAttribute()
    {
        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make([
            'payed' => false,
            'payed_credits' => 0
        ]);

        $this->assertFalse($SD->sro_available);

        // Payed
        $SD->payed = true;
        $this->assertTrue($SD->sro_available);

        // payed_credits > 1
        $SD->payed = false;
        $SD->payed_credits = 2;
        $this->assertTrue($SD->sro_available);
        $SD->payed_credits = 1;
        $this->assertFalse($SD->sro_available);
    }

    public function testHasOneWithResult()
    {
        $this->markTestIncomplete();
    }

    public function testHasExamAttempt()
    {
        $this->markTestIncomplete();
    }

    public function testSetTest1Trial()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['5credit'])->create(['student_id' => $user->id]);

        $SD->setTest1Trial();

        $SDFromDB = StudentDiscipline::where('id', $SD->id)->first();

        $this->assertTrue($SDFromDB->test1_result_trial);
        $this->assertFalse($SDFromDB->test1_qr_checked);

        $SD->delete();
        $user->forceDelete();
    }

    public function testSetFinalResultManual()
    {
        $this->markTestIncomplete();
    }

    public function testSetColor()
    {
        $this->markTestIncomplete();
    }

    public function testSetSROButtonEnabled()
    {
        $this->markTestIncomplete();
    }

    public function testGetNextRecommended()
    {
        $this->markTestIncomplete();
    }

    public function testGetPayedCreditSumAtCurrentSemester()
    {
        $this->markTestIncomplete();
    }

    public function testHasOpenDisciplinesCount()
    {
        $this->markTestIncomplete();
    }

    public function testSetPayed()
    {
        $this->markTestIncomplete();
    }

    public function testSetPayButtonEnabled()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreditsForFullBuy()
    {
        $this->markTestIncomplete();
    }

    public function testSetExamButtonShow()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user
            ->studentProfile()
            ->save(
                factory(Profiles::class)->states('active', 'fulltime')->make()
            );
        $user
            ->bcApplication()
            ->save(
                factory(BcApplications::class)->state(BcApplications::EDUCATION_HIGH_SCHOOL)->make()
            );

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->make([
            'student_id' => $user->id,
            'plan_semester' => $semesterString
        ]);

        $examSemester = new Semester();
        $examSemester->type = Semester::TYPE_EXAM;
        $examSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $examSemester->start_date = Carbon::today();
        $examSemester->end_date = Carbon::tomorrow();
        $examSemester->number = $semesterNumber;
        $examSemester->save();

        $examRetakeSemester = new Semester();
        $examRetakeSemester->type = Semester::TYPE_EXAM_RETAKE;
        $examRetakeSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $examRetakeSemester->start_date = Carbon::today();
        $examRetakeSemester->end_date = Carbon::tomorrow();
        $examRetakeSemester->number = $semesterNumber;
        $examRetakeSemester->save();

        // Check
        $SD->setExamButtonShow($user);
        $this->assertTrue($SD->examButtonShow);

        // Not ExamTime & ExamRetake
        $user->semesterDatesFlush();
        $examSemester->start_date = Carbon::tomorrow();
        $examSemester->save();
        $SD->setExamButtonShow($user);
        $this->assertTrue($SD->examButtonShow);

        // Not ExamTime & not ExamRetake
        $user->semesterDatesFlush();
        $examRetakeSemester->start_date = Carbon::tomorrow();
        $examRetakeSemester->save();
        $SD->setExamButtonShow($user);
        $this->assertFalse($SD->examButtonShow);

        // ExamTime & not ExamRetake
        $user->semesterDatesFlush();
        $examSemester->start_date = Carbon::today();
        $examSemester->save();
        $SD->setExamButtonShow($user);
        $this->assertTrue($SD->examButtonShow);

        // Practice

        $SD->discipline->is_practice = true;
        $SD->setExamButtonShow($user);
        $this->assertFalse($SD->examButtonShow);
        $SD->discipline->is_practice = false;
        $SD->setExamButtonShow($user);
        $this->assertTrue($SD->examButtonShow);

        // Diploma
        $SD->discipline->has_diplomawork = true;
        $SD->setExamButtonShow($user);
        $this->assertFalse($SD->examButtonShow);

        $user->forceDelete();
        $examSemester->delete();
        $examRetakeSemester->delete();
    }

    public function testGetId()
    {
        $this->markTestIncomplete();
    }

    public function testCheckValidQuizPay()
    {
        $this->markTestIncomplete();
    }

    public function testSetSyllabusButtonShow()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        /** @var User $user */
        $user = factory(User::class)->create(['keycloak' => 0]);
        $user
            ->studentProfile()
            ->save(
                factory(Profiles::class)->states('active', 'fulltime')->make()
            );
        $user
            ->bcApplication()
            ->save(
                factory(BcApplications::class)->state(BcApplications::EDUCATION_HIGH_SCHOOL)->make()
            );

        $discipline = factory(Discipline::class)->create();
        $discipline->syllabuses()->save(factory(Syllabus::class)->make());

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make([
            'student_id' => $user->id,
            'plan_semester' => $semesterString,
            'discipline_id' => $discipline->id
        ]);

        $syllabusSemester = new Semester();
        $syllabusSemester->type = Semester::TYPE_SYLLABUSES;
        $syllabusSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $syllabusSemester->start_date = Carbon::today();
        $syllabusSemester->end_date = Carbon::tomorrow();
        $syllabusSemester->number = $semesterNumber;
        $syllabusSemester->save();

        // Check
        $SD->setSyllabusButtonShow($user);
        $this->assertTrue($SD->syllabusButtonShow);

        $user->keycloak = 1;
        $SD->setSyllabusButtonShow($user);
        $this->assertFalse($SD->syllabusButtonShow);
        $user->keycloak = 0;

        $syllabusSemester->start_date = Carbon::tomorrow();
        $syllabusSemester->save();
        $user->semesterDatesFlush();
        $SD->setSyllabusButtonShow($user);
        $this->assertFalse($SD->syllabusButtonShow);
        $syllabusSemester->start_date = Carbon::today();
        $syllabusSemester->save();
        $user->semesterDatesFlush();

        $SD->setSyllabusButtonShow($user);
        $this->assertTrue($SD->syllabusButtonShow);

        $discipline->syllabuses()->forceDelete();

        $SD->setSyllabusButtonShow($user);
        $this->assertFalse($SD->syllabusButtonShow);

        $SD->delete();
        $discipline->forceDelete();
        $user->studentProfile()->delete();
        $user->forceDelete();
    }

    public function testSetPayCancelButtonShow()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        /** @var User $user */
        $user = factory(User::class)->create();
        $user
            ->studentProfile()
            ->save(
                factory(Profiles::class)->states('active', 'fulltime')->make()
            );
        $user
            ->bcApplication()
            ->save(
                factory(BcApplications::class)->state(BcApplications::EDUCATION_HIGH_SCHOOL)->make()
            );

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->make([
            'student_id' => $user->id,
            'plan_semester' => $semesterString,
            'payed_credits' => 1
        ]);

        $PCSemester = new Semester();
        $PCSemester->type = Semester::TYPE_BUY_CANCEL;
        $PCSemester->study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $PCSemester->start_date = Carbon::today();
        $PCSemester->end_date = Carbon::tomorrow();
        $PCSemester->number = $semesterNumber;
        $PCSemester->save();

        // Check
        $SD->setPayCancelButtonShow($user, []);
        $this->assertTrue($SD->payCancelButtonShow);

        $SD->payed_credits = null;
        $SD->setPayCancelButtonShow($user, []);
        $this->assertFalse($SD->payCancelButtonShow);
        $SD->payed_credits = 2;

        $SD->setPayCancelButtonShow($user, [$SD->discipline_id]);
        $this->assertFalse($SD->payCancelButtonShow);

        $PCSemester->start_date = Carbon::tomorrow();
        $PCSemester->save();
        $user->semesterDatesFlush();
        $SD->setPayCancelButtonShow($user, []);
        $this->assertFalse($SD->payCancelButtonShow);

        // Clear
        Semester::where('number', $semesterNumber)->delete();

        $SD->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();
    }
}