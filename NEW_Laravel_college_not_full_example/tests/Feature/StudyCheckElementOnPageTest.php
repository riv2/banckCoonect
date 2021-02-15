<?php

namespace Tests\Feature;


class StudyCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('study'));

        $response->assertSee('<div class="tab-pane active margin-t10" id="list">');
    }
}