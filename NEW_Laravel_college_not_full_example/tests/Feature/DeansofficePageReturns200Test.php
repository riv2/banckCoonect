<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeansofficePageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('deansOffice'));

        $response->assertStatus(200);
    }
}