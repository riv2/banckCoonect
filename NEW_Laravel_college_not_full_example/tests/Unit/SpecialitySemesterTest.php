<?php

namespace Tests\Unit;

use App\Semester;
use App\SpecialitySemester;
use Tests\TestCase;

class SpecialitySemesterTest extends TestCase
{

    public function testGetListForAdmin()
    {
        $data = SpecialitySemester::getListForAdmin();

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
        $this->assertIsString($data['data'][0][2]);
        $this->assertIsString($data['data'][0][3]);
        $this->assertIsInt($data['data'][0][4]);
        $this->assertRegExp('/\d\d\.\d\d\.\d\d\d\d/', $data['data'][0][5]);
        $this->assertRegExp('/\d\d\.\d\d\.\d\d\d\d/', $data['data'][0][6]);
        $this->assertIsInt($data['data'][0][7]);
    }

    public function testGetOne()
    {
        $specSemester = SpecialitySemester::first();

        if (!empty($specSemester)) {
            $specSemester2 = SpecialitySemester::getOne(
                $specSemester->speciality_id,
                $specSemester->study_form,
                $specSemester->base_education,
                $specSemester->semester,
                $specSemester->type
            );

            $this->assertNotEmpty($specSemester2);
            $this->assertSame($specSemester->id, $specSemester2->id);
        }
    }

    public function testGetDatesArray()
    {
        $specSemester = SpecialitySemester::first();

        if (!empty($specSemester)) {
            $dates = SpecialitySemester::getDatesArray(
                $specSemester->speciality_id,
                $specSemester->study_form,
                $specSemester->base_education,
                Semester::semesterString(Semester::CURRENT_STUDY_YEAR, $specSemester->semester),
                $specSemester->type
            );

            $this->assertNotEmpty($dates);
            $this->assertIsArray($dates);
            $this->assertCount(2, $dates);
            $this->assertSame($specSemester->start_date->format('Y-m-d 00:00:00'), $dates['start_date']);
            $this->assertSame($specSemester->end_date->format('Y-m-d 00:00:00'), $dates['end_date']);
        }
    }

    public function testGetAllByType()
    {
        $specSemesters = SpecialitySemester::getAllByType(Semester::TYPE_TEST1);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $specSemesters);

        if ($specSemesters->isNotEmpty()) {
            foreach ($specSemesters as $specSemester) {
                if ($specSemester->type !== Semester::TYPE_TEST1) {
                    $this->fail('Type is not same');
                }
            }
        }

        $type1Count = SpecialitySemester::where('type', Semester::TYPE_TEST1)->count();

        $this->assertSame($type1Count, $specSemesters->count());
    }

    public function testGetAllTest1()
    {
        $type = Semester::TYPE_TEST1;

        $specSemesters = SpecialitySemester::getAllTest1();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $specSemesters);

        if ($specSemesters->isNotEmpty()) {
            foreach ($specSemesters as $specSemester) {
                if ($specSemester->type !== $type) {
                    $this->fail('Type is not same');
                }
            }
        }

        $typeCount = SpecialitySemester::where('type', $type)->count();

        $this->assertSame($typeCount, $specSemesters->count());
    }

    public function testGetAllExam()
    {
        $type = Semester::TYPE_EXAM;

        $specSemesters = SpecialitySemester::getAllExam();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $specSemesters);

        if ($specSemesters->isNotEmpty()) {
            foreach ($specSemesters as $specSemester) {
                if ($specSemester->type !== $type) {
                    $this->fail('Type is not same');
                }
            }
        }

        $typeCount = SpecialitySemester::where('type', $type)->count();

        $this->assertSame($typeCount, $specSemesters->count());
    }

    public function testGetAllSRO()
    {
        $type = Semester::TYPE_SRO;

        $specSemesters = SpecialitySemester::getAllSRO();

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $specSemesters);

        if ($specSemesters->isNotEmpty()) {
            foreach ($specSemesters as $specSemester) {
                if ($specSemester->type !== $type) {
                    $this->fail('Type is not same');
                }
            }
        }

        $typeCount = SpecialitySemester::where('type', $type)->count();

        $this->assertSame($typeCount, $specSemesters->count());
    }
}
