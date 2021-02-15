<?php

namespace Tests\Feature;

use Tests\TestCase;

class ENTCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('studentEnt'));

        $response->assertSee('<div class="nav nav-tabs" id="nav-tab" role="tablist">');
    }
}