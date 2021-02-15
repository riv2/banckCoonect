<?php

namespace Tests\Feature;

use Tests\TestCase;

class FinancesPageReturns200Test extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('financesPanel'));

        $response->assertStatus(200);
    }
}