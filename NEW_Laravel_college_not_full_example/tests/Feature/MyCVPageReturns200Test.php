<?php

namespace Tests\Feature;

use Tests\TestCase;

class MyCVPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('vacancy.resume'));

        $response->assertStatus(200);
    }
}