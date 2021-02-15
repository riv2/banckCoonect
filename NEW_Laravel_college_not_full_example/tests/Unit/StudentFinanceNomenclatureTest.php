<?php

namespace Tests\Unit;

use App\FinanceNomenclature;
use App\StudentFinanceNomenclature;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class StudentFinanceNomenclatureTest extends TestCase
{

    public function testIsBought()
    {
        $this->markTestIncomplete();
    }

    public function testGetBoughtServiceIds()
    {
        $this->markTestIncomplete();
    }

    public function testAdd()
    {
        /** @var StudentFinanceNomenclature $SFN */
        $SFN = factory(StudentFinanceNomenclature::class)->make();

        /** @var FinanceNomenclature $financeNomenclature */
        $financeNomenclature = FinanceNomenclature::where('id', $SFN->finance_nomenclature_id)->first();

        /** @var StudentFinanceNomenclature $SFNInDB */
        $SFNInDB = StudentFinanceNomenclature::add(
            $SFN->user_id,
            $financeNomenclature,
            $SFN->semester,
            $SFN->balance_before,
            $SFN->student_discipline_id,
            $SFN->comment
        );

        $this->assertInstanceOf(StudentFinanceNomenclature::class, $SFNInDB);

        /** @var StudentFinanceNomenclature $SFNFromDB */
        $SFNFromDB = StudentFinanceNomenclature::where('id', $SFNInDB->id)->first();

        $this->assertSame($SFN->user_id, $SFNFromDB->user_id);
        $this->assertSame($SFN->finance_nomenclature_id, $SFNFromDB->finance_nomenclature_id);
        $this->assertSame($SFN->student_discipline_id, $SFNFromDB->student_discipline_id);
        $this->assertSame($SFN->comment, $SFNFromDB->comment);
        $this->assertSame($financeNomenclature->cost, $SFNFromDB->cost);
        $this->assertSame($SFN->semester, $SFNFromDB->semester);
        $this->assertSame($SFN->balance_before, $SFNFromDB->balance_before);

        $SFNFromDB->delete();
    }
}
