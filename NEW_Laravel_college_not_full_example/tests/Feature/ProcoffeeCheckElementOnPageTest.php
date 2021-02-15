<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProcoffeeCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('procoffee'));

        $response->assertSee('<h2 class="text-white no-margin">Procoffee</h2>');
    }
}