<?php

namespace Tests\Feature;

use Tests\TestCase;

class CafeteriaPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('cafeteria'));

        $response->assertStatus(200);
    }
}