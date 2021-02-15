<?php

namespace Tests\Feature;

use Tests\TestCase;

class VacancyPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('vacancy.index'));

        $response->assertSee('<div class="card shadow-sm p-3 mb-5 bg-white rounded">');
        $response->assertStatus(200);
    }
}