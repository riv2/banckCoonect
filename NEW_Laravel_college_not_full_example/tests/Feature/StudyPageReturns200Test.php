<?php

namespace Tests\Feature;


class StudyPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('study'));

        $response->assertStatus(200);
    }
}