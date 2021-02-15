<?php

namespace Tests\Feature;

use Tests\TestCase;

class GidCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('gid'));

        $response->assertSee('<p>');
    }
}