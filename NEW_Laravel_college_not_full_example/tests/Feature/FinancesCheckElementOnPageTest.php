<?php

namespace Tests\Feature;

use Tests\TestCase;

class FinancesCheckElementOnPageTest extends TestCaseAuth
{
    public function testBasic()
    {
        $response = $this->get(route('financesPanel'));

        $response->assertSee('<div id="balance" class="collapse show row" aria-labelledby="headingBalance" data-parent="#accordionFinance">');
    }
}