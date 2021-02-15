<?php

namespace Tests\Feature;

use Tests\TestCase;

class BusPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('bus'));

        $response->assertStatus(200);
    }
}