<?php


namespace Tests\Feature;


class QRPageCheckElementOnPage extends TestCaseAuth
{
    public function testBasic()
    {

        $headers = [
            'user-agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1'
        ];
        $response = $this->withServerVariables($this->transformHeadersToServerVars($headers))
            ->get(route('studentCheckin'));
        $response->assertSee('<input type="tel" id="numeric_code" maxlength="6" class="form-control numeric_code text-center margin-b15">');
    }
}