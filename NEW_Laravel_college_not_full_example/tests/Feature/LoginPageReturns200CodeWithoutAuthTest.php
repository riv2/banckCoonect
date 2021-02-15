<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginPageReturns200CodeWithoutAuthTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }
}