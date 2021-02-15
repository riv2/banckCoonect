<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProfileCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('userProfile'));

        $response->assertSee('<button class="btn btn-info" type="submit">');
    }
}