<?php

namespace Tests\Feature\Pp\Partner;

use App\Helpers\ArrayHelper;
use App\Helpers\PartnerProgramStorage;
use App\Lists\PpOnboardingList;
use App\Models\Pp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OffersTableSeeder;
use Tests\PpTestCase;

class IndexTest extends PpTestCase
{
    use RefreshDatabase;

    public static function getPp(): Pp
    {
        $PpOnboardingList = PpOnboardingList::getList();
        $PpOnboardingListForInterfaceTests = [];
        foreach ($PpOnboardingList as $key => $value) {
            if ($key != 'registered') {
                array_push($PpOnboardingListForInterfaceTests, $key);
            }
        }
        $pp = PartnerProgramStorage::getPP();
        $pp->onboarding_status = ArrayHelper::getRandomValue($PpOnboardingListForInterfaceTests);
        return $pp;
    }


    public function testPartnerCanViewAdsIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.ads'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewFaqCategoriesIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.faq.index'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewFeedsIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.feeds'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewLinksIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.links.index'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewNewsIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.news.index'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewOffersIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.offers.index'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewOrdersIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.orders.index'));
//dd($response->content());
        $response->assertStatus(200);
    }

    public function testPartnerCanViewPaymentsIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.payments'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewPostbackIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.postbacks'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewProfileIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.profile.index'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewReportIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.report'));

        $response->assertStatus(200);
    }

    public function testPartnerCanViewServicedeskIndexPage()
    {
        $pp = $this::getPp();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.servicedesk.index'));

        $response->assertStatus(200);
    }
}
