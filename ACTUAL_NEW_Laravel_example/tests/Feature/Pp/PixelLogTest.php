<?php

namespace Tests\Feature\Pp;

use App\Helpers\PartnerProgramStorage;
use App\Models\Click;
use App\Models\Link;
use App\Models\NotifyParam;
use App\Models\Offer;
use App\Models\OfferMaterial;
use App\Models\PixelLog;
use App\Models\Pp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Tests\TestCase;

class PixelLogTest extends TestCase
{
    use RefreshDatabase;

    public Pp $pp;

    protected function setUp(): void
    {
        $this->afterApplicationCreatedCallbacks[] = function () {
            $this->pp = new Pp();
            $this->pp->tech_domain = 'test.cloud.localhost';
            $this->pp->onboarding_status = 'first_partner_added';
            $this->pp->pp_target = 'lead';
            $this->pp->currency = 'RUB';
            $this->pp->lang = ['en' => false, 'es' => false, 'ru' => true];
            $this->pp->save();

            PartnerProgramStorage::setPP($this->pp);
            config(['app.url' => 'https://' . $this->pp->tech_domain]);
            URL::forceRootUrl('https://' . $this->pp->tech_domain);

            $this->partner = new User();
            $this->partner->name = 'partner';
            $this->partner->email = 'partner@test.ru';
            $this->partner->password = '';
            $this->partner->role = 'partner';
            $this->partner->pp_id = $this->pp->id;
            $this->partner->save();

            $this->notify_params = new NotifyParam();
            $this->notify_params->partner_id = $this->partner->id;
            $this->notify_params->fee_id = 'fee_id';
            $this->notify_params->type = 'default';
            $this->notify_params->postback_url = 'https://gocpa.requestcatcher.com/test';
            $this->notify_params->postback_auth = null;
            $this->notify_params->method = 'get';
            $this->notify_params->web_id = 'web_id';
            $this->notify_params->click_id = 'click_id';
            // $this->notify_params->gross_amount = 'gross_amount';
            // $this->notify_params->amount = 'amount';
            // $this->notify_params->status = 'status';
            // $this->notify_params->order_id = 'order_id';
            $this->notify_params->status_new_value = 'new';
            $this->notify_params->status_approve_value = 'approve';
            $this->notify_params->status_sale_value = 'sale';
            $this->notify_params->status_reject_value = 'reject';
            // $this->notify_params->status_transaction_value = 'transaction';
            $this->notify_params->save();

            $this->offer = new Offer();
            $this->offer->pp_id = $this->pp->id;
            $this->offer->offer_name = 'Тестовый оффер';
            $this->offer->model = 'sale';
            $this->offer->fee_type = 'fix';
            $this->offer->link_template = 'utm_medium=cpa&utm_source=partners&utm_campaign={$link_id}&utm_content={$partner_id}&utm_term={WEB_ID}&click_id={CLICK_ID}';
            $this->offer->save();

            $this->offermaterial = new OfferMaterial();
            $this->offermaterial->offer_id = $this->offer->id;
            $this->offermaterial->name = 'Main page';
            $this->offermaterial->material_type = 'landing';
            $this->offermaterial->status = 1;
            $this->offermaterial->material_params = ["link" => "https://example.com/"];
            $this->offermaterial->save();

            $this->link = new Link();
            $this->link->pp_id = $this->pp->id;
            $this->link->partner_id = $this->partner->id;
            $this->link->link_name = 'link';
            $this->link->link = '';
            $this->link->offer_id = $this->offer->id;
            $this->link->offer_materials_id = $this->offermaterial->offer_material_id;
            $this->link->status = 'ACTIVE';
            $this->link->save();
        };

        parent::setUp();
    }

    /**
     * Тестирует создание клика пикселем
     *
     * @return void
     */
    public function testPixelCreated()
    {
        $uid = Str::uuid()->toString();
        $dl = parse_url($this->link->link);

        $data = [
            'dl' => $dl['scheme'] . '://' . $dl['host'],
            'ev' => 'pageload',
            'uid' => $uid,
            'click_id' => null,
            'utm_term' => null,
            'utm_medium' => 'cpa',
            'utm_source' => 'partners',
            'utm_content' => (string) $this->partner->id,
            'utm_campaign' => (string) $this->link->id,
        ];
        $response = $this->postJson('cpapixel.gif', $data);
        $response->assertSuccessful();
        $response->assertNoContent();
        $response->assertHeader('x-pixel-id');

        $pixel_id = $response->headers->get('x-pixel-id');

        $this->assertDatabaseHas('pixel_log', ['id' => $pixel_id]);
    }

    /**
     * Тестирует создание клика пикселем
     *
     * @return void
     */
    public function testPixelCreatedClick()
    {
        $uid = Str::uuid()->toString();

        $data = [
            'dl' => $this->link->link,
            'ev' => 'pageload',
            'uid' => $uid,
            'click_id' => null,
            'utm_term' => null,
            'utm_medium' => 'cpa',
            'utm_source' => 'partners',
            'utm_content' => (string) $this->partner->id,
            'utm_campaign' => (string) $this->link->id,
        ];
        $response = $this->postJson('cpapixel.gif', $data);
        $response->assertSuccessful();
        $response->assertNoContent();
        $response->assertHeader('x-pixel-id');

        $pixel_id = $response->headers->get('x-pixel-id');

        $this->assertDatabaseHas('pixel_log', ['id' => $pixel_id]);
        $this->assertDatabaseHas('clicks', ['client_id' => $uid, 'pixel_log_id' => $pixel_id]);
    }

    /**
     * Тестирует создание клика пикселем
     *
     * @return void
     */
    public function testPixelCreatedOrder()
    {
        $uid = Str::uuid()->toString();
        $dl = parse_url($this->link->link);

        $data = [
            'dl' => $dl['scheme'] . '://' . $dl['host'],
            'ev' => 'purchase',
            'ed' => '{"order_id": 42}',
            'uid' => $uid,
            'click_id' => null,
            'utm_term' => null,
            'utm_medium' => 'cpa',
            'utm_source' => 'partners',
            'utm_content' => (string) $this->partner->id,
            'utm_campaign' => (string) $this->link->id,
        ];
        $response = $this->postJson('cpapixel.gif', $data);
        $response->assertSuccessful();
        $response->assertNoContent();
        $response->assertHeader('x-pixel-id');

        $pixel_id = $response->headers->get('x-pixel-id');

        $this->assertDatabaseHas('pixel_log', ['id' => $pixel_id]);
        $this->assertDatabaseHas('clicks', ['client_id' => $uid, 'pixel_log_id' => $pixel_id]);
        $this->assertDatabaseHas('orders', ['order_id' => 42, 'client_id' => $uid, 'pixel_id' => $pixel_id]);
        $this->assertDatabaseHas('notify', ['partner_id' => $this->partner->id]);
    }
}
