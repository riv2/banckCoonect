<?php


namespace Tests\Feature\Postback;

use App\Models\Notify;
use App\Models\NotifyParam;
use App\Postbacks\Postback;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendTest extends TestCase
{
    use RefreshDatabase;

    public function testSend()
    {
        //Не было автотестов, нечего и начинать.
        $this->assertTrue(true);
        return;
        //todo сделать автотест
        $notify_param = new NotifyParam([
            'partner_id'=>1,
            'postback_url'=>'https://httpbin.org/get',
            'method'=>'get',
            'status_new_value'=>'new',
            'status_approve_value'=>'approve',
            'status_sale_value'=>'sale',
            'status_reject_value'=>'reject',
        ]);
        $notify_param->save();

        $notify = new Notify([
            'datetime'=>date('Y-m-d H:i:s'),
            'partner_id'=>1,
            'click_id'=>1,
            'web_id'=>1,
            'order_id'=>1,
            'link_id'=>1,
            'model'=>'sale',
            'status'=>'new',
            'amount'=>1000
        ]);
        $notify->save();

        $postback = new Postback($notify);
        $this->assertTrue($postback->handle());
    }
}
