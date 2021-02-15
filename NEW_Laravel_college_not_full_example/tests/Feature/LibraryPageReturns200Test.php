<?php

namespace Tests\Feature;

use Tests\TestCase;

class LibraryPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('library.page'));

        $response->assertStatus(200);
    }
}