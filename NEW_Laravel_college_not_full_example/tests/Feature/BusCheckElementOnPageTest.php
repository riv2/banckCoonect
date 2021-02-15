<?php

namespace Tests\Feature;

use Tests\TestCase;

class BusCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('bus'));

        $response->assertSee('<div class="card shadow-sm p-3 mb-5 bg-white rounded">');
    }
}