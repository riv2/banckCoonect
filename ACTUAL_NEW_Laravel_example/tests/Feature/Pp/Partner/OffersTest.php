<?php

namespace Tests\Feature\Pp\Partner;

use App\Helpers\PartnerProgramStorage;
use App\Models\Offer;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OfferMaterialsSeeder;
use OffersTableSeeder;
use Tests\PpTestCase;

class OffersTest extends PpTestCase
{
    use RefreshDatabase;

    public function testPartnerCanViewOffersList()
    {
        $pp = PartnerProgramStorage::getPP();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/partner/offers');

        $response->assertSuccessful();
        $response->assertSeeText(__('partners.offers.index.offers_list'));
    }

    public function testPartnerCanViewOfferWithoutLanding()
    {
        $pp = PartnerProgramStorage::getPP();
        $this->seed(OffersTableSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/partner/offers');

        $response->assertSuccessful();
        // $response->assertDontSeeText(__('offers.materials.landing.get_link'));
    }

    public function testPartnerCanViewOfferWithLanding()
    {
        $pp = PartnerProgramStorage::getPP();
        $this->seed(OffersTableSeeder::class);
        $this->seed(OfferMaterialsSeeder::class);
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/partner/offers');

        $response->assertSuccessful();
        $response->assertSeeText(__('partners.offers.index.offers_list'));
        // $response->assertSeeText(__('partners.offers.materials.landing.get_link'));
    }

    public function testPartnerCanViewOwnOffer()
    {
        $pp = PartnerProgramStorage::getPP();
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);
        $offer = factory(Offer::class)->create();

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.offers.show', $offer));

        $response->assertSuccessful();
        $response->assertSeeText($offer->offer_name);
    }

    public function testPartnerCantViewAnotherOffer()
    {
        $pp = PartnerProgramStorage::getPP();
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);
        $offer = factory(Offer::class)->create([
            'pp_id' => 32
        ]);

        $response = $this->actingAs($user, 'web')
            ->get(route('partner.offers.show', $offer));

        $response->assertNotFound();
    }
}
