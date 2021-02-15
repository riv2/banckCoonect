<?php

namespace Tests\Feature;

use Tests\TestCase;

class PollPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('students.polls.show'));

        $response->assertStatus(200);
    }
}