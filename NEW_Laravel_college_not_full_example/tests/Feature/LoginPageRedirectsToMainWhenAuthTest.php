<?php

namespace Tests\Feature;

use App\User;

class LoginPageRedirectsToMainWhenAuthTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get('/login');

        $response->assertStatus(302)
            ->assertLocation('/');
    }
}