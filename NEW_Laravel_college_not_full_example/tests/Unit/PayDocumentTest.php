<?php

namespace Tests\Unit;

use App\PayDocument;

//use PHPUnit\Framework\TestCase;
use App\PayDocumentStudentDiscipline;
use Tests\TestCase;

class PayDocumentTest extends TestCase
{

    public function testCreateForWifi()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForStudentDiscipline()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForStudentRetakeKge()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForLecture()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForStudentRetakeTest()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForBalance()
    {
        $this->markTestIncomplete();
    }

    public function testCreateTest1Trial()
    {
        /** @var PayDocument $PD */
        $PD = factory(PayDocument::class)->make();

        $added = PayDocument::createTest1Trial(
            $PD->user_id,
            $PD->amount,
            $PD->student_discipline_id,
            $PD->balance_before
        );

        $this->assertTrue($added);

        /** @var PayDocument $PDFromDB */
        $PDFromDB = PayDocument
            ::where('student_discipline_id', $PD->student_discipline_id)
            ->where('user_id', $PD->user_id)
            ->orderBy('id', 'desc')
            ->first();

        $this->assertSame($PD->amount, $PDFromDB->amount);
        $this->assertSame($PD->balance_before, $PDFromDB->balance_before);
        $this->assertSame(PayDocument::STATUS_SUCCESS, $PDFromDB->status);
        $this->assertSame(1, $PDFromDB->complete_pay);

        $PDSD = PayDocumentStudentDiscipline::where('pay_document_id', $PDFromDB->id)->first();
        $this->assertNotNull($PDSD);

        PayDocumentStudentDiscipline::where('pay_document_id', $PDFromDB->id)->forceDelete();
        $PDFromDB->forceDelete();
    }

    public function testCreateForLectureRoom()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForTest()
    {
        $this->markTestIncomplete();
    }

    public function testCreateForProfile()
    {
        $this->markTestIncomplete();
    }

    public function testChangePayStatus()
    {
        $this->markTestIncomplete();
    }

    public function testCreateExamTrial()
    {
        $this->markTestIncomplete();
    }
}
