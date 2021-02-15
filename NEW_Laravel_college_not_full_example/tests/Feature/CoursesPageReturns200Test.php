<?php

namespace Tests\Feature;

use Tests\TestCase;

class CoursesPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('getCoursesList'));

        $response->assertStatus(200);
    }
}