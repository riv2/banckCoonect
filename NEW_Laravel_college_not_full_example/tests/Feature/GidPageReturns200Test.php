<?php

namespace Tests\Feature;

use Tests\TestCase;

class GidPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('gid'));

        $response->assertStatus(200);
    }
}