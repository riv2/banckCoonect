<?php

namespace Tests\Feature;

use Tests\TestCase;

class LibraryCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('library.page'));

        $response->assertSee('<label for="staticEmail" class="col-sm-2 col-form-label">');
    }
}