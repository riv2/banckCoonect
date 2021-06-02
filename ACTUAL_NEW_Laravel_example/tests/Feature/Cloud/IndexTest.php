<?php

namespace Tests\Feature\Cloud;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяет, отдается ли
     *
     * @return void
     */
    public function testCloudIndex()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSeeText(__('register-advert.welcome'));
        // dump($response->getContent());
    }

    /**
     * Проверка на то, что юзер может авторизоваться в облаке
     *
     * @return void
     */
    public function testManagerCanLoginWithCorrectCredentials()
    {
        $user = factory(User::class)->create([
            'password' => Hash::make($password = 'i-love-laravel'),
            'role' => 'manager',
            'pp_id' => 'null',
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => $password,

        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('manager.report'));
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
}
