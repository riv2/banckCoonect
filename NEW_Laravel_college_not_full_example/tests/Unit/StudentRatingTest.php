<?php

namespace Tests\Unit;

use App\Services\StudentRating;

//use PHPUnit\Framework\TestCase;
use Tests\TestCase;

class StudentRatingTest extends TestCase
{

    public function testGetLetter()
    {
        $this->assertNull(StudentRating::getLetter(-1));
        $this->assertNull(StudentRating::getLetter(101));
        $this->assertIsString(StudentRating::getLetter(0));
        $this->assertIsString(StudentRating::getLetter(100));
        $this->assertSame('F', StudentRating::getLetter(0));
        $this->assertSame('A', StudentRating::getLetter(100));
    }

    public function testGetDisciplineGpa()
    {
        $this->assertNull(StudentRating::getDisciplineGpa(-1, 5));
        $this->assertNull(StudentRating::getDisciplineGpa(101, 5));
        $this->assertSame(0.0, StudentRating::getDisciplineGpa(0, 5));
        $this->assertSame(4.0, StudentRating::getDisciplineGpa(100, 1));
        $this->assertSame(20.0, StudentRating::getDisciplineGpa(100, 5));
    }

    public function testGetFinalResultPoints()
    {
        $this->assertNull(StudentRating::getFinalResultPoints(-1));
        $this->assertNull(StudentRating::getFinalResultPoints(101));
        $this->assertIsFloat(StudentRating::getFinalResultPoints(0));
        $this->assertIsFloat(StudentRating::getFinalResultPoints(100));
        $this->assertSame(0.0, StudentRating::getFinalResultPoints(0));
        $this->assertIsFloat(4.0, StudentRating::getFinalResultPoints(100));
    }

    public function testGetClassicString()
    {
        $this->markTestIncomplete();
    }

    public function testGetClassicString3Lang()
    {
        $this->markTestIncomplete();
    }
}
