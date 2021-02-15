<?php

namespace Tests\Feature;

use Tests\TestCase;

class MainPageRedirectsToLoginWithoutAuthTest extends TestCase
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
        $response->assertLocation('/login');
    }
}