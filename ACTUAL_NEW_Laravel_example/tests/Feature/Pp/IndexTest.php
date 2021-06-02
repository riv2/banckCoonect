<?php

namespace Tests\Feature\Pp;

use App\Helpers\PartnerProgramStorage;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\PpTestCase;

class IndexTest extends PpTestCase
{
    use RefreshDatabase;

    /**
     * Проверка на то, что работает главная у партнерки по тех-домену
     *
     * @return void
     */
    public function testUserCanViewPartnerProgramIndexByTechDomain()
    {
        $pp = PartnerProgramStorage::getPP();
        $response = $this->get('https://' . $pp->tech_domain . '/');
        $response->assertStatus(200);
        $response->assertSee($pp->long_name);
        // dd($response->getContent());
    }

    /**
     * Проверка на то, что работает главная у партнерки по прод-домену
     *
     * @return void
     */
    public function testUserCanViewPartnerProgramIndexByProdDomain()
    {
        $pp = PartnerProgramStorage::getPP();
        $response = $this->get('https://' . $pp->prod_domain . '/');
        $response->assertStatus(200);
        $response->assertSee($pp->long_name);
        // dd($response->getContent());
    }

    /**
     * Проверка на то, что юзер может авторизоваться в облаке
     *
     * @return void
     */
    public function testAdvertiserCanLoginWithCorrectCredentials()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make($password = 'i-love-laravel'),
            'role' => 'advertiser',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('advertiser.report'));
    }

    /**
     * Проверка на то, что юзер может авторизоваться в облаке
     *
     * @return void
     */
    public function testPartnerCanLoginWithCorrectCredentials()
    {
        $pp = PartnerProgramStorage::getPP();
        $user = factory(User::class)->create([
            'password' => Hash::make($password = 'i-love-laravel'),
            'role' => 'partner',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('partner.report'));
    }
}
