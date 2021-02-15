<?php

namespace Tests\Feature;

class VacancyResumePageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('vacancy.resume'));

        $response->assertStatus(200);
    }
}
