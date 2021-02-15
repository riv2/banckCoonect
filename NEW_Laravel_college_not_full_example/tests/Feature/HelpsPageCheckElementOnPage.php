<?php


namespace Tests\Feature;


class HelpsPageCheckElementOnPage extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('helps'));

        $response->assertSee('<div class="card shadow-sm p-3 mb-5 bg-white rounded">');
    }
}