<?php

namespace Tests\Feature;

use App\User;

class MainPageReturns200WhenAuthTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertSee('Обучение');
    }
}