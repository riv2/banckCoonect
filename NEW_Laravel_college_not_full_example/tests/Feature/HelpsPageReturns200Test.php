<?php

namespace Tests\Feature;

use Tests\TestCase;

class HelpsPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('helps'));

        $response->assertStatus(200);
    }
}