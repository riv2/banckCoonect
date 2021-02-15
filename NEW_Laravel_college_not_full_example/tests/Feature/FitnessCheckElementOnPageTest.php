<?php

namespace Tests\Feature;

use Tests\TestCase;

class FitnessCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('fitnessRoom'));

        $response->assertSee('<div class="card shadow-sm p-3 mb-5 bg-white rounded">');
    }
}