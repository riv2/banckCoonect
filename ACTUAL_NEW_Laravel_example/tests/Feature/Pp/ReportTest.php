<?php

namespace Tests\Feature\Pp;

use App\Helpers\PartnerProgramStorage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\PpTestCase;

class ReportTest extends PpTestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPartnerRedirectingToReport()
    {
        $user = factory(User::class)->create([
            'role' => 'partner',
        ]);
        $response = $this->actingAs($user)->get('/');

        $response->assertRedirect(route('partner.report'));
    }

    public function testPartnerCanViewReport()
    {
        $pp = PartnerProgramStorage::getPP();
        $user = factory(User::class)->create([
            'role' => 'partner',
            'pp_id' => $pp->id,
        ]);

        $response = $this->actingAs($user, 'web')
            ->get('/partner/report');

        $response->assertSuccessful();
    }
}
