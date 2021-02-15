<?php

namespace Tests\Unit;

use App\Profiles;
use App\Semester;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SemesterTest extends TestCase
{
    public function testCurrentStudyYearConstant() : void
    {
        Semester::CURRENT_STUDY_YEAR;

        $now = Carbon::now();
        $september1 = Carbon::now()->day(1)->month(9);

        // After 1 september
        if ($now >= $september1) {
            $this->assertTrue(Semester::CURRENT_STUDY_YEAR == $now->year);
        } else {
            $this->assertTrue(Semester::CURRENT_STUDY_YEAR == $now->year - 1);
        }
    }

    public function testInStudyYear()
    {
        $wrongStudyForm = Semester::inStudyYear('bla');
        $this->assertNull($wrongStudyForm);

        // Semester 1
        $date = Carbon::createFromFormat('Y-m-d', Semester::CURRENT_STUDY_YEAR . '-09-20');
        $semester = Semester::inStudyYear(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $date);
        $this->assertIsInt($semester);
        $this->assertSame(1, $semester);

        // Semester 2
        $date = Carbon::createFromFormat('Y-m-d', (Semester::CURRENT_STUDY_YEAR + 1) . '-03-20');
        $semester = Semester::inStudyYear(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $date);
        $this->assertIsInt($semester);
        $this->assertSame(2, $semester);

        // Semester 3
        // TODO Seeder for study 3 semester dates
//        $date = Carbon::createFromFormat('Y-m-d', Semester::CURRENT_STUDY_YEAR . '-06-30');
//        $semester = Semester::inStudyYear(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $date);
//        $this->assertIsInt($semester);
//        $this->assertEquals(3, $semester);
    }

    public function testGetListForAdmin()
    {
        $data = Semester::getListForAdmin();

        $this->assertIsArray($data);

        $this->assertArrayHasKey('recordsTotal', $data);
        $this->assertIsInt($data['recordsTotal']);

        $this->assertArrayHasKey('recordsFiltered', $data);
        $this->assertIsInt($data['recordsFiltered']);

        $this->assertArrayHasKey('data', $data);
        $this->assertIsArray($data['data']);

        $this->assertTrue(count($data['data']) > 0);

        $this->assertIsString($data['data'][0][0]);
        $this->assertIsString($data['data'][0][1]);
        $this->assertIsInt($data['data'][0][2]);
        $this->assertRegExp('/\d\d\.\d\d\.\d\d\d\d/', $data['data'][0][3]);
        $this->assertRegExp('/\d\d\.\d\d\.\d\d\d\d/', $data['data'][0][4]);
        $this->assertIsInt($data['data'][0][5]);
    }

    public function testGetListForAdminStudyFormFilter()
    {
        $data = Semester::getListForAdmin(Profiles::EDUCATION_STUDY_FORM_FULLTIME, null, null, 0, 1000);

        $values = [];
        foreach ($data['data'] as $datum) {
            $values[] = $datum[0];
        }

        $this->assertSame(1, count(array_unique($values)));
    }

    public function testGetListForAdminTypeFilter()
    {
        $data = Semester::getListForAdmin(null, Semester::TYPE_STUDY, null, 0, 1000);

        $values = [];
        foreach ($data['data'] as $datum) {
            $values[] = $datum[1];
        }

        $this->assertSame(1, count(array_unique($values)));
    }

    public function testGetListForAdminSemesterFilter()
    {
        $data = Semester::getListForAdmin(null, null, 1, 0, 1000);

        $values = [];
        foreach ($data['data'] as $datum) {
            $values[] = $datum[2];
        }

        $this->assertSame(1, count(array_unique($values)));
    }

    public function testIsExamTime()
    {
        $semester = Semester::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('type', Semester::TYPE_EXAM)
            ->first();

        $this->assertTrue(Semester::isExamTime(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->start_date));

        $this->assertTrue(Semester::isExamTime(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->end_date));
    }

    public function testSemesterString()
    {
        $this->assertSame('2019-20.1', Semester::semesterString(2019, 1));
        $this->assertSame('2020-21.2', Semester::semesterString(2020, 2));
        $this->assertSame('2021-22.3', Semester::semesterString(2021, 3));
    }

    public function testTodayInDefaultDates()
    {
        // Semester 1
        $semester = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 1)
            ->where('type', Semester::TYPE_TEST1)
            ->first();

        $this->assertTrue(Semester::todayInDefaultDates(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 1),
            Semester::TYPE_TEST1,
            $semester->start_date)
        );

        $this->assertTrue(Semester::todayInDefaultDates(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 1),
            Semester::TYPE_TEST1,
            $semester->end_date)
        );

        // Semester 2
        $semester = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 2)
            ->where('type', Semester::TYPE_TEST1)
            ->first();

        $this->assertTrue(Semester::todayInDefaultDates(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 2),
            Semester::TYPE_TEST1,
            $semester->start_date)
        );

        $this->assertTrue(Semester::todayInDefaultDates(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 2),
            Semester::TYPE_TEST1,
            $semester->end_date)
        );
    }

    public function testByStringSemester()
    {
        $this->assertSame(1, Semester::byStringSemester(2019, '2019-20.1'));
        $this->assertSame(2, Semester::byStringSemester(2019, '2019-20.2'));
        $this->assertNull(Semester::byStringSemester(2019, '2019-20.3'));

        $this->assertSame(3, Semester::byStringSemester(2018, '2019-20.1'));
        $this->assertSame(4, Semester::byStringSemester(2018, '2019-20.2'));
        $this->assertNull(Semester::byStringSemester(2018, '2019-20.3'));
    }

    public function testGetNumberFromString()
    {
        $this->assertSame(1, Semester::getNumberFromString('2019-20.1'));
        $this->assertSame(2, Semester::getNumberFromString('2019-20.2'));
        $this->assertSame(3, Semester::getNumberFromString('2019-20.3'));
    }

    public function testIsSROTime()
    {
        $semester = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('type', Semester::TYPE_SRO)
            ->first();

        $this->assertTrue(Semester::isSROTime(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->start_date));

        $this->assertTrue(Semester::isSROTime(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->end_date));
    }

    public function testIsTest1Time()
    {
        $semester = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('type', Semester::TYPE_TEST1)
            ->first();

        $this->assertTrue(Semester::isTest1Time(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->start_date));

        $this->assertTrue(Semester::isTest1Time(Profiles::EDUCATION_STUDY_FORM_FULLTIME, $semester->end_date));
    }

    public function testTodayBetween()
    {
        $date = Carbon::createFromFormat('Y-m-d', '2019-09-02');

        $this->assertFalse(Semester::todayBetween('2019-09-03 10:00:00', '2019-09-04 10:00:00', $date));
        $this->assertFalse(Semester::todayBetween('2019-09-01 10:00:00', '2019-09-01 10:00:00', $date));

        $this->assertTrue(Semester::todayBetween('2019-09-01 10:00:00', '2019-09-03 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2019-09-01 10:00:00', '2019-09-02 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2019-09-02 10:00:00', '2019-09-03 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2019-09-02 10:00:00', '2019-09-02 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2019-09-02 10:00:00', '2019-09-02 9:00:00', $date));

        $date = Carbon::createFromFormat('Y-m-d', '2020-01-01');

        $this->assertTrue(Semester::todayBetween('2019-12-31 10:00:00', '2020-01-02 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2019-12-31 10:00:00', '2020-01-01 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2020-01-01 10:00:00', '2020-01-02 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2020-01-01 10:00:00', '2020-01-01 10:00:00', $date));
        $this->assertTrue(Semester::todayBetween('2020-01-01 10:00:00', '2020-01-01 9:00:00', $date));
    }

    public function testInSpeciality()
    {
        $semester1 = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 1)
            ->where('type', Semester::TYPE_STUDY)
            ->first();

        $this->assertSame(1, Semester::inSpeciality(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::CURRENT_STUDY_YEAR,
            $semester1->start_date
        ));
        $this->assertSame(3, Semester::inSpeciality(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::CURRENT_STUDY_YEAR - 1,
            $semester1->start_date
        ));

        $semester2 = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 2)
            ->where('type', Semester::TYPE_STUDY)
            ->first();

        $this->assertSame(2, Semester::inSpeciality(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::CURRENT_STUDY_YEAR,
            $semester2->start_date
        ));

        $this->assertSame(4, Semester::inSpeciality(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            Semester::CURRENT_STUDY_YEAR - 1,
            $semester2->start_date
        ));

        // TODO
//        $semester3 = Semester
//            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
//            ->where('number', 3)
//            ->where('type', Semester::TYPE_STUDY)
//            ->first();
//
//        $this->assertSame(3, Semester::inSpeciality(
//            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
//            Semester::CURRENT_STUDY_YEAR,
//            $semester3->start_date
//        ));
    }

    public function testCurrent()
    {
        $semester1 = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 1)
            ->where('type', Semester::TYPE_STUDY)
            ->first();

        $this->assertSame(Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 1), Semester::current(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            $semester1->start_date
        ));

        $semester2 = Semester
            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
            ->where('number', 2)
            ->where('type', Semester::TYPE_STUDY)
            ->first();

        $this->assertSame(Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 2), Semester::current(
            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
            $semester2->start_date
        ));

//        $semester3 = Semester
//            ::where('study_form', Profiles::EDUCATION_STUDY_FORM_FULLTIME)
//            ->where('number', 3)
//            ->where('type', Semester::TYPE_STUDY)
//            ->first();
//
//        $this->assertSame(Semester::semesterString(Semester::CURRENT_STUDY_YEAR, 3), Semester::current(
//            Profiles::EDUCATION_STUDY_FORM_FULLTIME,
//            $semester3->start_date
//        ));
    }

    public function testCheckStringSemester1()
    {
        $this->assertNull(Semester::checkStringSemester('2019-20.1'));
        $this->assertNull(Semester::checkStringSemester('2019-20.2'));
        $this->assertNull(Semester::checkStringSemester('2019-20.3'));

        $this->expectException(\Exception::class);
        Semester::checkStringSemester('123');
    }

    public function testCheckStringSemester2()
    {
        $this->expectException(\Exception::class);
        Semester::checkStringSemester(1);
    }

    public function testCheckStringSemester3()
    {
        $this->expectException(\Exception::class);
        Semester::checkStringSemester('1');
    }

    public function testCheckStringSemester4()
    {
        $this->expectException(\Exception::class);
        Semester::checkStringSemester('2019-20');
    }

    public function testCheckStringSemester5()
    {
        $this->expectException(\Exception::class);
        Semester::checkStringSemester('2019-20.4');
    }

    public function testSemesterInCurrentYear()
    {
        $this->assertSame('2019-20.1', Semester::semesterInCurrentYear(1));
        $this->assertSame('2019-20.2', Semester::semesterInCurrentYear(2));
        $this->assertSame('2019-20.3', Semester::semesterInCurrentYear(3));
    }

    public function testGetFinishedYesterdayTest1()
    {
        $testSemester = factory(Semester::class)->state('test1_yesterday_ended')->create();

        $endedSemesters = Semester::getFinishedYesterdayTest1(Carbon::now());

        $this->assertIsIterable($endedSemesters);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $endedSemesters);
        $this->assertNotEmpty($endedSemesters);

        $exists = false;

        foreach ($endedSemesters as $endedSemester) {
            if ($endedSemester->id == $testSemester->id) {
                $exists = true;
            }
        }

        $this->assertTrue($exists, 'Could not find ended semester');

        $testSemester->delete();
    }

    public function testGetFinishedYesterdayExam()
    {
        $testSemester = factory(Semester::class)->state('exam_yesterday_ended')->create();

        $endedSemesters = Semester::getFinishedYesterdayExam(Carbon::now());

        $this->assertIsIterable($endedSemesters);
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $endedSemesters);
        $this->assertNotEmpty($endedSemesters);

        $exists = false;

        foreach ($endedSemesters as $endedSemester) {
            if ($endedSemester->id == $testSemester->id) {
                $exists = true;
            }
        }

        $this->assertTrue($exists, 'Could not find ended semester');

        $testSemester->delete();
    }
}