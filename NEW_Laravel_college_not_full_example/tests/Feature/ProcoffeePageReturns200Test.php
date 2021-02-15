<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProcoffeePageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('procoffee'));

        $response->assertStatus(200);
    }
}