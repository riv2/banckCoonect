<?php

namespace Tests\Feature;

use Tests\TestCase;

class ENTPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('studentEnt'));

        $response->assertStatus(200);
    }
}