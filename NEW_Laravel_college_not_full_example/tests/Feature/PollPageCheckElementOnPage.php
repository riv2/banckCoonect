<?php


namespace Tests\Feature;


class PollPageCheckElementOnPage extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('students.polls.show'));

        $response->assertSee('<ul class="list-group list-group-flush">');
    }
}