<?php

namespace Tests\Unit;

use App\BcApplications;
use App\Profiles;
use App\Semester;
use App\Speciality;
use App\SpecialitySemester;
use App\StudentDiscipline;
use App\StudentGpa;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserTest extends TestCase
{

    public function testAgitatorFullBalance()
    {
        $this->markTestIncomplete();
    }

    public function testGetUserListForPollAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testSetAdmissionYearAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetBaseEducationAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testStudentDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testChangeBalance()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreatedTimestamp()
    {
        $this->markTestIncomplete();
    }

    public function testGetListForAdminStudyPlan()
    {
        $this->markTestIncomplete();
    }

    public function testRoles()
    {
        $this->markTestIncomplete();
    }

    public function testGetFreeRemoteAccessAttribute()
    {
        /** @var User $user */
        $user = factory(User::class)->make();
        $user->studentProfile = factory(Profiles::class)->states('active', 'online')->make();
        $user->studentProfile->speciality = factory(Speciality::class)->state('2019')->make();

        $this->assertTrue($user->free_remote_access);

        // year < 2019
        $user->studentProfile->speciality->year = 2018;
        $this->assertFalse($user->free_remote_access);
        $user->studentProfile->speciality->year = 2019;
        $this->assertTrue($user->free_remote_access);

        // fulltime
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;
        $this->assertFalse($user->free_remote_access);

        // evening
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_EVENING;
        $this->assertFalse($user->free_remote_access);

        // extramural
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL;
        $this->assertFalse($user->free_remote_access);
    }

    public function testUsersMoveToInspection()
    {
        $this->markTestIncomplete();
    }

    public function testGetPromotionStatus()
    {
        $this->markTestIncomplete();
    }

    public function testGetIin()
    {
        $this->markTestIncomplete();
    }

    public function testSetZeroExamBySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $semester = factory(Semester::class)->state('exam')->create(['number' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );

//        $SDWithoutT1Practice = factory(StudentDiscipline::class)->state('practice')->create([
//            'student_id' => $user->id,
//            'test1_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
//        $SDWithoutT1Diploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
//            'student_id' => $user->id,
//            'test1_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
        $SDWithoutExam_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExam_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithExam = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExamWrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExamStudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutExamAdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );
        $user2SDWithoutExam = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroExamBySemester($semester, [$user2->id]);

        /** @var StudentDiscipline $SDWithoutExamPracticeFromDb */
//        $SDWithoutExamPracticeFromDb = StudentDiscipline::where('id', $SDWithoutExamPractice->id)->first();
//        $this->assertNull($SDWithoutExamPracticeFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamDiplomaFromDb */
//        $SDWithoutExamDiplomaFromDb = StudentDiscipline::where('id', $SDWithoutExamDiploma->id)->first();
//        $this->assertNull($SDWithoutExamDiplomaFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamFromDb_1 */
        $SDWithoutExamFromDb_1 = StudentDiscipline::where('id', $SDWithoutExam_1->id)->first();
        $this->assertSame(0, $SDWithoutExamFromDb_1->test_result);
        $this->assertTrue($SDWithoutExamFromDb_1->exam_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutExamFromDb_2 */
        $SDWithoutExamFromDb_2 = StudentDiscipline::where('id', $SDWithoutExam_2->id)->first();
        $this->assertSame(0, $SDWithoutExamFromDb_2->test_result);
        $this->assertTrue($SDWithoutExamFromDb_2->exam_zeroed_by_time);

        /** @var StudentDiscipline $SDWithExamFromDb */
        $SDWithExamFromDb = StudentDiscipline::where('id', $SDWithExam->id)->first();
        $this->assertSame(50, $SDWithExamFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamWrongSemesterFromDb */
        $SDWithoutExamWrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutExamWrongSemester->id)->first();
        $this->assertNull($SDWithoutExamWrongSemesterFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamStudentNotConfirmedFromDb */
        $SDWithoutExamStudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutExamStudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutExamStudentNotConfirmedFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamAdminNotConfirmedFromDb */
        $SDWithoutExamAdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutExamAdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutExamAdminNotConfirmedFromDb->test_result);

        /** @var StudentDiscipline $user2SDWithoutExamFromDb */
        $user2SDWithoutExamFromDb = StudentDiscipline::where('id', $user2SDWithoutExam->id)->first();
        $this->assertNull($user2SDWithoutExamFromDb->test_result);

        User::setZeroExamBySemester($semester, []);

        /** @var StudentDiscipline $user2SDWithoutExamFromDb */
        $user2SDWithoutExamFromDb = StudentDiscipline::where('id', $user2SDWithoutExam->id)->first();
        $this->assertSame(0, $user2SDWithoutExamFromDb->test_result);
        $this->assertTrue($user2SDWithoutExamFromDb->exam_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testAgitatorUsers()
    {
        $this->markTestIncomplete();
    }

    public function testForumBanned()
    {
        $this->markTestIncomplete();
    }

    public function testHasPayedDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testGetLastSemesterInStudyPlanAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testIsAgitator()
    {
        $this->markTestIncomplete();
    }

    public function testHasClientRole()
    {
        $this->markTestIncomplete();
    }

    public function testBalance()
    {
        $this->markTestIncomplete();
    }

    public function testGetRemoteAccessPriceAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testRefreshBalance()
    {
        $this->markTestIncomplete();
    }

    public function testAgitatorTestFinalRegistration()
    {
        $this->markTestIncomplete();
    }

    public function testGetStudyPlanAdminConfirmedAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetSemesterCreditsLimitAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetCurrentRole()
    {
        $this->markTestIncomplete();
    }

    public function testGetCallbackPhone()
    {
        $this->markTestIncomplete();
    }

    public function testSetZeroTest1BySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $semester = factory(Semester::class)->state('test1')->create(['number' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );

        $SDWithoutT1Practice = factory(StudentDiscipline::class)->state('practice')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1Diploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithT1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1WrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1StudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutT1AdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );
        $user2SDWithoutT1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroTest1BySemester($semester, [$user2->id]);

        /** @var StudentDiscipline $SDWithoutT1PracticeFromDb */
        $SDWithoutT1PracticeFromDb = StudentDiscipline::where('id', $SDWithoutT1Practice->id)->first();
        $this->assertNull($SDWithoutT1PracticeFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1DiplomaFromDb */
        $SDWithoutT1DiplomaFromDb = StudentDiscipline::where('id', $SDWithoutT1Diploma->id)->first();
        $this->assertNull($SDWithoutT1DiplomaFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb_1 */
        $SDWithoutT1FromDb_1 = StudentDiscipline::where('id', $SDWithoutT1_1->id)->first();
        $this->assertSame(0, $SDWithoutT1FromDb_1->test1_result);
        $this->assertTrue($SDWithoutT1FromDb_1->test1_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutT1FromDb_2 */
        $SDWithoutT1FromDb_2 = StudentDiscipline::where('id', $SDWithoutT1_2->id)->first();
        $this->assertSame(0, $SDWithoutT1FromDb_2->test1_result);
        $this->assertTrue($SDWithoutT1FromDb_2->test1_zeroed_by_time);

        /** @var StudentDiscipline $SDWithT1FromDb */
        $SDWithT1FromDb = StudentDiscipline::where('id', $SDWithT1->id)->first();
        $this->assertSame(50, $SDWithT1FromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1WrongSemesterFromDb */
        $SDWithoutT1WrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutT1WrongSemester->id)->first();
        $this->assertNull($SDWithoutT1WrongSemesterFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1StudentNotConfirmedFromDb */
        $SDWithoutT1StudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutT1StudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutT1StudentNotConfirmedFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1AdminNotConfirmedFromDb */
        $SDWithoutT1AdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutT1AdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutT1AdminNotConfirmedFromDb->test1_result);

        /** @var StudentDiscipline $user2SDWithoutT1FromDb */
        $user2SDWithoutT1FromDb = StudentDiscipline::where('id', $user2SDWithoutT1->id)->first();
        $this->assertNull($user2SDWithoutT1FromDb->test1_result);

        User::setZeroTest1BySemester($semester, []);

        /** @var StudentDiscipline $user2SDWithoutT1FromDb */
        $user2SDWithoutT1FromDb = StudentDiscipline::where('id', $user2SDWithoutT1->id)->first();
        $this->assertSame(0, $user2SDWithoutT1FromDb->test1_result);
        $this->assertTrue($user2SDWithoutT1FromDb->test1_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testGetGpaAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testResume()
    {
        $this->markTestIncomplete();
    }

    public function testTeacherDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testLanguageEnglishLevels()
    {
        $this->markTestIncomplete();
    }

    public function testFinanceOperations()
    {
        $this->markTestIncomplete();
    }

    public function testSetRole()
    {
        $this->markTestIncomplete();
    }

    public function testStudentCheckins()
    {
        $this->markTestIncomplete();
    }

    public function testHasAccess()
    {
        $this->markTestIncomplete();
    }

    public function testGetAdminDisciplineIdListAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testRefreshSearchAdminMatriculants()
    {
        $this->markTestIncomplete();
    }

    public function testPromotions()
    {
        $this->markTestIncomplete();
    }

    public function testSearchUsersForQuizUsersTable()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateGpa()
    {
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['1credit', 'finished'])->make();
        $SD->final_result_gpa = 1.5;
        $user->studentDisciplines()->save($SD);

        $SD = factory(StudentDiscipline::class)->states(['2credit', 'finished'])->make();
        $SD->final_result_gpa = 2.5;
        $user->studentDisciplines()->save($SD);

        $SD = factory(StudentDiscipline::class)->states(['5credit', 'finished'])->make();
        $SD->final_result_gpa = 3.1;
        $user->studentDisciplines()->save($SD);

        $user->updateGpa();

        /** @var StudentGpa $gpa */
        $gpa = StudentGpa::where('user_id', $user->id)->first();

        $this->assertSame(0.89, $gpa->value);

        $gpa->delete();
        $user->studentDisciplines()->delete();
        $user->forceDelete();
    }

    public function testSendPhoneConfirmCode()
    {
        $this->markTestIncomplete();
    }

    public function testGetGuestListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testDecree()
    {
        $this->markTestIncomplete();
    }

    public function testEntranceTests()
    {
        $this->markTestIncomplete();
    }

    public function testSpentOnCredits()
    {
        $this->markTestIncomplete();
    }

    public function testGetLanguageEnglishLevelAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetStudyYearAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testEmployeeUser()
    {
        $this->markTestIncomplete();
    }

    public function testGetBdate()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateGuestSearchCache()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateSearchCache()
    {
        $this->markTestIncomplete();
    }

    public function testGetAdmissionYearAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testIsSRORetakeTime()
    {
        $this->markTestIncomplete();
    }

    public function testActivity_logs()
    {
        $this->markTestIncomplete();
    }

    public function testQuizeResultKges()
    {
        $this->markTestIncomplete();
    }

    public function testHasAgitatorRole()
    {
        $this->markTestIncomplete();
    }

    public function testAgitatorRefunds()
    {
        $this->markTestIncomplete();
    }

    public function testIsTime()
    {
        $semesterNumber = 9;
        $semesterString = Semester::semesterString(2019, $semesterNumber);

        // Clear
        SpecialitySemester::where('semester', $semesterNumber)->delete();
        Semester::where('number', $semesterNumber)->delete();

        $type = Semester::TYPE_TEST1;

        /** @var User $user */
        $user = factory(User::class)->make();
        $user->studentProfile = factory(Profiles::class)->states('active')->make();
        $user->bcApplication = factory(BcApplications::class)->make();

        $specSemester = new SpecialitySemester;
        $specSemester->speciality_id = $user->studentProfile->education_speciality_id;
        $specSemester->study_form = $user->studentProfile->education_study_form;
        $specSemester->base_education = $user->bcApplication->education;
        $specSemester->semester = 9;
        $specSemester->type = $type;
        $specSemester->start_date = Carbon::today();
        $specSemester->end_date = Carbon::tomorrow();
        $specSemester->save();

        // Speciality Semester exists and today
        $this->assertTrue($user->isTest1Time($semesterString));

        // Speciality Semester exists and not today
        $specSemester->start_date = Carbon::tomorrow();
        $specSemester->save();
        $user->semesterDatesFlush();
        $this->assertFalse($user->isTest1Time($semesterString));

        // Speciality Semester does not exist and default is today
        $specSemester->delete();
        $user->semesterDatesFlush();

        $semester = new Semester();
        $semester->type = $type;
        $semester->study_form = $user->studentProfile->education_study_form;
        $semester->start_date = Carbon::today();
        $semester->end_date = Carbon::tomorrow();
        $semester->number = $semesterNumber;
        $semester->save();

        $this->assertTrue($user->isTest1Time($semesterString));

        // Speciality Semester does not exist and default is not today
        $semester->start_date = Carbon::tomorrow();
        $semester->save();
        $user->semesterDatesFlush();
        $this->assertFalse($user->isTest1Time($semesterString));

        $semester->delete();
    }

    public function testGetStudentForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testGetIdsBySpecialitySemesters()
    {
        $specSemester1 = factory(SpecialitySemester::class)->state('test1')->create(['semester' => 9]);

        $user1 = factory(User::class)->create();
        $user1->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester1->speciality_id,
                'education_study_form' => $specSemester1->study_form
            ])
        );
        $user1->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester1->base_education,
            ])
        );

        $specSemester2 = factory(SpecialitySemester::class)->state('test1')->create(['semester' => 9]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester2->speciality_id,
                'education_study_form' => $specSemester2->study_form
            ])
        );
        $user2->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester2->base_education,
            ])
        );

        $userIds = User::getIdsBySpecialitySemesters(collect([$specSemester1, $specSemester2]));

        $this->assertIsArray($userIds);
        $this->assertTrue(count($userIds) >= 2);
        $this->assertIsInt($userIds[0]);
        $this->assertTrue(in_array($user1->id, $userIds));
        $this->assertTrue(in_array($user2->id, $userIds));

        $user1->bcApplication()->delete();
        $user1->studentProfile()->delete();
        $user1->forceDelete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
        $specSemester1->delete();
        $specSemester2->delete();
    }

    public function testHasTeacherMirasRole()
    {
        $this->markTestIncomplete();
    }

    public function testPayCards()
    {
        $this->markTestIncomplete();
    }

    public function testIsBuyingTime()
    {
        $this->markTestIncomplete();
    }

    public function testHasRight()
    {
        $this->markTestIncomplete();
    }

    public function testHasRole()
    {
        $this->markTestIncomplete();
    }

    public function testHasAdminRole()
    {
        $this->markTestIncomplete();
    }

    public function testHasAcademDebt()
    {
        $this->markTestIncomplete();
    }

    public function testEducationDocumentList()
    {
        $this->markTestIncomplete();
    }

    public function testGetAgitatorUserPayStatus()
    {
        $this->markTestIncomplete();
    }

    public function testGetNotificationCount()
    {
        $this->markTestIncomplete();
    }

    public function testGetFioAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testAttachPayCard()
    {
        $this->markTestIncomplete();
    }

    public function testGetMatriculantListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testGetPromotionWork()
    {
        $this->markTestIncomplete();
    }

    public function testDefaultCitizenshipId()
    {
        $this->markTestIncomplete();
    }

    public function testTeacherStudyGroups()
    {
        $this->markTestIncomplete();
    }

    public function testBalanceByDebt()
    {
        $this->markTestIncomplete();
    }

    public function testAdminComments()
    {
        $this->markTestIncomplete();
    }

    public function testGetMigrationMaxFreeCreditsAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetMigrationMaxNotFreeCreditsAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testUsersMoveToOR()
    {
        $this->markTestIncomplete();
    }

    public function testGetPhoneNumberAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testGetByIIN()
    {
        $this->markTestIncomplete();
    }

    public function testHasEducationDocument()
    {
        $this->markTestIncomplete();
    }

    public function testUnsetRole()
    {
        $this->markTestIncomplete();
    }

    public function testUpdateSimpleSearchCache()
    {
        $this->markTestIncomplete();
    }

    public function testTeacherProfile()
    {
        $this->markTestIncomplete();
    }

    public function testGetQuizeResultKgeAttribute()
    {
        $this->markTestIncomplete();
    }

    public function testSetZeroExamBySpecialitySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $specSemester = factory(SpecialitySemester::class)->state('exam')->create(['semester' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );

//        $SDWithoutExamPractice = factory(StudentDiscipline::class)->state('practice')->create([
//            'student_id' => $user->id,
//            'test_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
//        $SDWithoutExamDiploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
//            'student_id' => $user->id,
//            'test_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
        $SDWithoutExam_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExam_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithExam = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExamWrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutExamStudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutExamAdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user2->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );
        $user2SDWithoutExam = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'test_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroExamBySpecialitySemester($specSemester);

        /** @var StudentDiscipline $SDWithoutExamPracticeFromDb */
//        $SDWithoutExamPracticeFromDb = StudentDiscipline::where('id', $SDWithoutExamPractice->id)->first();
//        $this->assertNull($SDWithoutExamPracticeFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamDiplomaFromDb */
//        $SDWithoutExamDiplomaFromDb = StudentDiscipline::where('id', $SDWithoutExamDiploma->id)->first();
//        $this->assertNull($SDWithoutExamDiplomaFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamFromDb_1 */
        $SDWithoutExamFromDb_1 = StudentDiscipline::where('id', $SDWithoutExam_1->id)->first();
        $this->assertSame(0, $SDWithoutExamFromDb_1->test_result);
        $this->assertTrue($SDWithoutExamFromDb_1->exam_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutExamFromDb_2 */
        $SDWithoutExamFromDb_2 = StudentDiscipline::where('id', $SDWithoutExam_2->id)->first();
        $this->assertSame(0, $SDWithoutExamFromDb_2->test_result);
        $this->assertTrue($SDWithoutExamFromDb_2->exam_zeroed_by_time);

        /** @var StudentDiscipline $SDWithExamFromDb */
        $SDWithExamFromDb = StudentDiscipline::where('id', $SDWithExam->id)->first();
        $this->assertSame(50, $SDWithExamFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamWrongSemesterFromDb */
        $SDWithoutExamWrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutExamWrongSemester->id)->first();
        $this->assertNull($SDWithoutExamWrongSemesterFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamStudentNotConfirmedFromDb */
        $SDWithoutExamStudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutExamStudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutExamStudentNotConfirmedFromDb->test_result);

        /** @var StudentDiscipline $SDWithoutExamAdminNotConfirmedFromDb */
        $SDWithoutExamAdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutExamAdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutExamAdminNotConfirmedFromDb->test_result);

        /** @var StudentDiscipline $user2SDWithoutExamFromDb */
        $user2SDWithoutExamFromDb = StudentDiscipline::where('id', $user2SDWithoutExam->id)->first();
        $this->assertSame(0, $user2SDWithoutExamFromDb->test_result);
        $this->assertTrue($user2SDWithoutExamFromDb->exam_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testPracticeStudentDocuments()
    {
        $this->markTestIncomplete();
    }

    public function testStudentProfile()
    {
        $this->markTestIncomplete();
    }

    public function testSubmodules()
    {
        $this->markTestIncomplete();
    }

    public function testGetTeachersForSelect()
    {
        $this->markTestIncomplete();
    }

    public function testQuizResults()
    {
        $this->markTestIncomplete();
    }

    public function testGetTeacherDisciplineGroups()
    {
        $this->markTestIncomplete();
    }

    public function testGetAdmissionDate()
    {
        $this->markTestIncomplete();
    }

    public function testCheckIgnoreConfirmMobile()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreditsLimit()
    {
        $this->markTestIncomplete();
    }

    public function testStudentAllQuizSuccess()
    {
        $this->markTestIncomplete();
    }

    public function testEducationDocumentFirst()
    {
        $this->markTestIncomplete();
    }

//    public function testGetSpecialityAdmissionYearAttribute()
//    {
//
//    }

    public function testSetZeroSROBySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $semester = factory(Semester::class)->state('exam')->create(['number' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );

//        $SDWithoutT1Practice = factory(StudentDiscipline::class)->state('practice')->create([
//            'student_id' => $user->id,
//            'test1_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
//        $SDWithoutT1Diploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
//            'student_id' => $user->id,
//            'test1_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
        $SDWithoutSRO_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSRO_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithSRO = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSROWrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSROStudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutSROAdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_study_form' => $semester->study_form
            ])
        );
        $user2SDWithoutSRO = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroSROBySemester($semester, [$user2->id]);

        /** @var StudentDiscipline $SDWithoutSROPracticeFromDb */
//        $SDWithoutSROPracticeFromDb = StudentDiscipline::where('id', $SDWithoutSROPractice->id)->first();
//        $this->assertNull($SDWithoutSROPracticeFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSRODiplomaFromDb */
//        $SDWithoutSRODiplomaFromDb = StudentDiscipline::where('id', $SDWithoutSRODiploma->id)->first();
//        $this->assertNull($SDWithoutSRODiplomaFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROFromDb_1 */
        $SDWithoutSROFromDb_1 = StudentDiscipline::where('id', $SDWithoutSRO_1->id)->first();
        $this->assertSame(0, $SDWithoutSROFromDb_1->task_result);
        $this->assertTrue($SDWithoutSROFromDb_1->sro_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutSROFromDb_2 */
        $SDWithoutSROFromDb_2 = StudentDiscipline::where('id', $SDWithoutSRO_2->id)->first();
        $this->assertSame(0, $SDWithoutSROFromDb_2->task_result);
        $this->assertTrue($SDWithoutSROFromDb_2->sro_zeroed_by_time);

        /** @var StudentDiscipline $SDWithSROFromDb */
        $SDWithSROFromDb = StudentDiscipline::where('id', $SDWithSRO->id)->first();
        $this->assertSame(50, $SDWithSROFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROWrongSemesterFromDb */
        $SDWithoutSROWrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutSROWrongSemester->id)->first();
        $this->assertNull($SDWithoutSROWrongSemesterFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROStudentNotConfirmedFromDb */
        $SDWithoutSROStudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutSROStudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutSROStudentNotConfirmedFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROAdminNotConfirmedFromDb */
        $SDWithoutSROAdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutSROAdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutSROAdminNotConfirmedFromDb->task_result);

        /** @var StudentDiscipline $user2SDWithoutSROFromDb */
        $user2SDWithoutSROFromDb = StudentDiscipline::where('id', $user2SDWithoutSRO->id)->first();
        $this->assertNull($user2SDWithoutSROFromDb->task_result);

        User::setZeroSROBySemester($semester, []);

        /** @var StudentDiscipline $user2SDWithoutSROFromDb */
        $user2SDWithoutSROFromDb = StudentDiscipline::where('id', $user2SDWithoutSRO->id)->first();
        $this->assertSame(0, $user2SDWithoutSROFromDb->task_result);
        $this->assertTrue($user2SDWithoutSROFromDb->sro_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testAgitatorAvailableBalance()
    {
        $this->markTestIncomplete();
    }

    public function testHasTeacherRole()
    {
        $this->markTestIncomplete();
    }

    public function testSetZeroSROBySpecialitySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $specSemester = factory(SpecialitySemester::class)->state('sro')->create(['semester' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );

//        $SDWithoutExamPractice = factory(StudentDiscipline::class)->state('practice')->create([
//            'student_id' => $user->id,
//            'test_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
//        $SDWithoutExamDiploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
//            'student_id' => $user->id,
//            'test_result' => null,
//            'plan_semester' => Semester::semesterInCurrentYear(9),
//            'plan_admin_confirm' => true,
//            'plan_student_confirm' => true
//        ]);
        $SDWithoutSRO_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSRO_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithSRO = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSROWrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutSROStudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutSROAdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user2->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );
        $user2SDWithoutSRO = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'task_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroSROBySpecialitySemester($specSemester);

        /** @var StudentDiscipline $SDWithoutSROPracticeFromDb */
//        $SDWithoutSROPracticeFromDb = StudentDiscipline::where('id', $SDWithoutSROPractice->id)->first();
//        $this->assertNull($SDWithoutSROPracticeFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSRODiplomaFromDb */
//        $SDWithoutSRODiplomaFromDb = StudentDiscipline::where('id', $SDWithoutSRODiploma->id)->first();
//        $this->assertNull($SDWithoutSRODiplomaFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROFromDb_1 */
        $SDWithoutSROFromDb_1 = StudentDiscipline::where('id', $SDWithoutSRO_1->id)->first();
        $this->assertSame(0, $SDWithoutSROFromDb_1->task_result);
        $this->assertTrue($SDWithoutSROFromDb_1->sro_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutSROFromDb_2 */
        $SDWithoutSROFromDb_2 = StudentDiscipline::where('id', $SDWithoutSRO_2->id)->first();
        $this->assertSame(0, $SDWithoutSROFromDb_2->task_result);
        $this->assertTrue($SDWithoutSROFromDb_2->sro_zeroed_by_time);

        /** @var StudentDiscipline $SDWithSROFromDb */
        $SDWithSROFromDb = StudentDiscipline::where('id', $SDWithSRO->id)->first();
        $this->assertSame(50, $SDWithSROFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROWrongSemesterFromDb */
        $SDWithoutSROWrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutSROWrongSemester->id)->first();
        $this->assertNull($SDWithoutSROWrongSemesterFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROStudentNotConfirmedFromDb */
        $SDWithoutSROStudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutSROStudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutSROStudentNotConfirmedFromDb->task_result);

        /** @var StudentDiscipline $SDWithoutSROAdminNotConfirmedFromDb */
        $SDWithoutSROAdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutSROAdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutSROAdminNotConfirmedFromDb->task_result);

        /** @var StudentDiscipline $user2SDWithoutSROFromDb */
        $user2SDWithoutSROFromDb = StudentDiscipline::where('id', $user2SDWithoutSRO->id)->first();
        $this->assertSame(0, $user2SDWithoutSROFromDb->task_result);
        $this->assertTrue($user2SDWithoutSROFromDb->sro_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testGpaList()
    {
        $this->markTestIncomplete();
    }

    public function testDebtTrusts()
    {
        $this->markTestIncomplete();
    }

    public function testMgApplication()
    {
        $this->markTestIncomplete();
    }

    public function testWifi()
    {
        $this->markTestIncomplete();
    }

    public function testGetWithdrawInfo()
    {
        $this->markTestIncomplete();
    }

    public function testNotifications()
    {
        $this->markTestIncomplete();
    }

    public function testTeacherGroups()
    {
        $this->markTestIncomplete();
    }

    public function testCheckPhoneConfirmCode()
    {
        $this->markTestIncomplete();
    }

    public function testHasListenerCourseRole()
    {
        $this->markTestIncomplete();
    }

    public function testGetCreditPrice()
    {
        $this->markTestIncomplete();
    }

    public function testSemesterDatesFlush()
    {
        $this->markTestIncomplete();
    }

    public function testHelpRequests()
    {
        $this->markTestIncomplete();
    }

    public function testSetIgnoreConfirmMobile()
    {
        $this->markTestIncomplete();
    }

    public function testCourses()
    {
        $this->markTestIncomplete();
    }

    public function testGetDisciplineGpaSum()
    {
        /** @var User $user */
        $user = factory(User::class)->create();

        /** @var StudentDiscipline $SD */
        $SD = factory(StudentDiscipline::class)->states(['finished'])->make();
        $SD->final_result_gpa = 1.5;
        $user->studentDisciplines()->save($SD);

        $SD = factory(StudentDiscipline::class)->states(['finished'])->make();
        $SD->final_result_gpa = 2.5;
        $user->studentDisciplines()->save($SD);

        $SD = factory(StudentDiscipline::class)->states(['finished'])->make();
        $SD->final_result_gpa = 3.1;
        $user->studentDisciplines()->save($SD);

        $this->assertSame(7.1, $user->getDisciplineGpaSum());

        $user->studentDisciplines()->delete();
        $user->forceDelete();
    }

    public function testGetDistanceLearningAttribute()
    {
        /** @var User $user */
        $user = factory(User::class)->make();
        $user->studentProfile = factory(Profiles::class)->make();
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_FULLTIME;

        $this->assertFalse($user->distance_learning);

        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL;
        $this->assertTrue($user->distance_learning);
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_EVENING;
        $this->assertTrue($user->distance_learning);
        $user->studentProfile->education_study_form = Profiles::EDUCATION_STUDY_FORM_ONLINE;
        $this->assertTrue($user->distance_learning);
    }

    public function testBcApplication()
    {
        $this->markTestIncomplete();
    }

    public function testSetZeroTest1BySpecialitySemester()
    {
        /** @var SpecialitySemester $specSemester */
        $specSemester = factory(SpecialitySemester::class)->state('test1')->create(['semester' => 9]);

        $user = factory(User::class)->create();
        $user->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );

        $SDWithoutT1Practice = factory(StudentDiscipline::class)->state('practice')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1Diploma = factory(StudentDiscipline::class)->state('diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1_1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1_2 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithT1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => 50,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1WrongSemester = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(3),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);
        $SDWithoutT1StudentNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => false
        ]);
        $SDWithoutT1AdminNotConfirmed = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => false,
            'plan_student_confirm' => false
        ]);

        $user2 = factory(User::class)->create();
        $user2->studentProfile()->save(
            factory(Profiles::class)->make([
                'education_speciality_id' => $specSemester->speciality_id,
                'education_study_form' => $specSemester->study_form
            ])
        );
        $user2->bcApplication()->save(
            factory(BcApplications::class)->make([
                'education' => $specSemester->base_education,
            ])
        );
        $user2SDWithoutT1 = factory(StudentDiscipline::class)->state('not_practice_not_diploma_work')->create([
            'student_id' => $user2->id,
            'test1_result' => null,
            'plan_semester' => Semester::semesterInCurrentYear(9),
            'plan_admin_confirm' => true,
            'plan_student_confirm' => true
        ]);

        User::setZeroTest1BySpecialitySemester($specSemester);

        /** @var StudentDiscipline $SDWithoutT1PracticeFromDb */
        $SDWithoutT1PracticeFromDb = StudentDiscipline::where('id', $SDWithoutT1Practice->id)->first();
        $this->assertNull($SDWithoutT1PracticeFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1DiplomaFromDb */
        $SDWithoutT1DiplomaFromDb = StudentDiscipline::where('id', $SDWithoutT1Diploma->id)->first();
        $this->assertNull($SDWithoutT1DiplomaFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb_1 */
        $SDWithoutT1FromDb_1 = StudentDiscipline::where('id', $SDWithoutT1_1->id)->first();
        $this->assertSame(0, $SDWithoutT1FromDb_1->test1_result);
        $this->assertTrue($SDWithoutT1FromDb_1->test1_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutT1FromDb_2 */
        $SDWithoutT1FromDb_2 = StudentDiscipline::where('id', $SDWithoutT1_2->id)->first();
        $this->assertSame(0, $SDWithoutT1FromDb_2->test1_result);
        $this->assertTrue($SDWithoutT1FromDb_2->test1_zeroed_by_time);

        /** @var StudentDiscipline $SDWithoutT1FromDb */
        $SDWithT1FromDb = StudentDiscipline::where('id', $SDWithT1->id)->first();
        $this->assertSame(50, $SDWithT1FromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb */
        $SDWithoutT1WrongSemesterFromDb = StudentDiscipline::where('id', $SDWithoutT1WrongSemester->id)->first();
        $this->assertNull($SDWithoutT1WrongSemesterFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb */
        $SDWithoutT1StudentNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutT1StudentNotConfirmed->id)->first();
        $this->assertNull($SDWithoutT1StudentNotConfirmedFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb */
        $SDWithoutT1AdminNotConfirmedFromDb = StudentDiscipline::where('id', $SDWithoutT1AdminNotConfirmed->id)->first();
        $this->assertNull($SDWithoutT1AdminNotConfirmedFromDb->test1_result);

        /** @var StudentDiscipline $SDWithoutT1FromDb */
        $user2SDWithoutT1FromDb = StudentDiscipline::where('id', $user2SDWithoutT1->id)->first();
        $this->assertSame(0, $user2SDWithoutT1FromDb->test1_result);
        $this->assertTrue($user2SDWithoutT1FromDb->test1_zeroed_by_time);

        $user->studentDisciplines()->delete();
        $user->bcApplication()->delete();
        $user->studentProfile()->delete();
        $user->forceDelete();

        $user2->studentDisciplines()->delete();
        $user2->bcApplication()->delete();
        $user2->studentProfile()->delete();
        $user2->forceDelete();
    }

    public function testPositions()
    {
        $this->markTestIncomplete();
    }

    public function testDisciplines()
    {
        $this->markTestIncomplete();
    }

    public function testGetStudentListForAdmin()
    {
        $this->markTestIncomplete();
    }

    public function testGetDisciplineCreditSum()
    {
        $this->markTestIncomplete();
    }
}
