<?php

namespace Tests\Feature;

use Tests\TestCase;

class PoolPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('pool'));

        $response->assertStatus(200);
    }
}