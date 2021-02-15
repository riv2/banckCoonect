<?php

namespace Tests\Feature;

use Tests\TestCase;

class CafeteriaCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('cafeteria'));

        $response->assertSee('<video class="col-12 margin-t20 margin-b20" loop autoplay controls="true"');
    }
}