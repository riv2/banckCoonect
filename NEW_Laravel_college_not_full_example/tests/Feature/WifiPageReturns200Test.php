<?php

namespace Tests\Feature;

use Tests\TestCase;

class WifiPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('wifi'));

        $response->assertStatus(200);
    }
}