<?php

namespace Tests\Unit;

use App\QrCode;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class QrCodeTest extends TestCase
{

    public function testGenerate()
    {
        $this->markTestIncomplete();
    }

    public function testIsNumericCodeValid()
    {
        $numericCode = '123456';

        /** @var QrCode $QRCode */
        $QRCode = factory(QrCode::class)->create(['numeric_code' => $numericCode]);

        $this->assertTrue(QrCode::isNumericCodeValid($QRCode->discipline_id, $numericCode));
        $this->assertFalse(QrCode::isNumericCodeValid($QRCode->discipline_id, '111111'));

        $QRCode->where('teacher_id', $QRCode->teacher_id)->where('discipline_id', $QRCode->discipline_id)->delete();
    }

    public function testGet()
    {
        $this->markTestIncomplete();
    }

    public function testGetByNumericCode()
    {
        $this->markTestIncomplete();
    }

    public function testGenerateCode()
    {
        $this->markTestIncomplete();
    }

    public function testIsValid()
    {
        /** @var QrCode $QRCode */
        $QRCode = factory(QrCode::class)->create();

        $this->assertTrue(QrCode::isValid($QRCode->discipline_id, $QRCode->code));
        $this->assertFalse(QrCode::isValid($QRCode->discipline_id, '111111'));

        $QRCode->where('teacher_id', $QRCode->teacher_id)->where('discipline_id', $QRCode->discipline_id)->delete();
    }
}
