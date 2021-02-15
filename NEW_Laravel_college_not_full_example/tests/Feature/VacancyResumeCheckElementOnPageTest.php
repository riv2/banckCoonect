<?php

namespace Tests\Feature;

class VacancyResumeCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('vacancy.resume'));

        $response->assertSee('<div class="card shadow-sm p-3 mb-5 bg-white rounded">');
    }
}
