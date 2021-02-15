<?php

namespace Tests\Feature;

use Tests\TestCase;

class PoolCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('pool'));

        $response->assertSee('<video class="col-12 margin-t20 margin-b20" loop autoplay controls="true"');
    }
}