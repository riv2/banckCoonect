<?php

namespace Tests\Feature;

use Tests\TestCase;

class FitnessPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('fitnessRoom'));

        $response->assertStatus(200);
    }
}