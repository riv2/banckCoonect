<?php

namespace Tests\Feature;

use Tests\TestCase;

class ForumIndexPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('chatter.home'));

        $response->assertStatus(200);
    }
}