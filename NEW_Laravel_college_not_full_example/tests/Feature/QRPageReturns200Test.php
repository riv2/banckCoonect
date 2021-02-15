<?php

namespace Tests\Feature;

use Tests\TestCase;

class QRPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('studentCheckin'));

        $response->assertStatus(200);
    }
}