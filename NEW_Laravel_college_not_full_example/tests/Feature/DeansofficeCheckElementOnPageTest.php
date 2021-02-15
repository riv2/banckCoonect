<?php

namespace Tests\Feature;

use Tests\TestCase;

class DeansofficeCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('deansOffice'));

        $response->assertSee('<button id="usedSubmit" type="submit" class="btn btn-info">');
    }
}