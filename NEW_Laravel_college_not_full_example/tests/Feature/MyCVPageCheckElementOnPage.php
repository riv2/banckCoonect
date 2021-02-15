<?php


namespace Tests\Feature;


class MyCVPageCheckElementOnPage extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('vacancy.resume'));

        $response->assertSee('<h2 class="text-white no-margin">');
    }
}