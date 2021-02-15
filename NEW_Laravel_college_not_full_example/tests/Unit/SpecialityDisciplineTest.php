<?php

namespace Tests\Unit;

use App\SpecialityDiscipline;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class SpecialityDisciplineTest extends TestCase
{

    public function testGetOne()
    {
        /** @var SpecialityDiscipline $SD */
        $SD = SpecialityDiscipline::first();

        $SD1 = SpecialityDiscipline::getOne($SD->speciality_id, $SD->discipline_id);

        $this->assertSame($SD->id, $SD1->id);
    }

    public function testGetLanguageType()
    {
        $this->markTestIncomplete();
    }

    public function testGetSemester()
    {
        $this->markTestIncomplete();
    }

    public function testGetLangForSRO()
    {
        $this->markTestIncomplete();
    }

    public function testIsExist()
    {
        $this->markTestIncomplete();
    }

    public function testGetDisciplineIdsExcludingIds()
    {
        $this->markTestIncomplete();
    }
}
