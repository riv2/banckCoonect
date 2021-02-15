<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfilePageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('userProfile'));

        $response->assertStatus(200);
    }
}