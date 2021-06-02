<?php

namespace Tests\Feature\Pp\Auth;

use App\Helpers\PartnerProgramStorage;
use App\Models\Pp;
use App\Notifications\VerifyEmail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdvertRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewCloudIndex()
    {
        $response = $this->get('/');
        $response->assertSuccessful();
        $response->assertViewIs('gocpa_cloud.welcome');
    }

    public function testAdvertCannotRegisterWithoutProvidingFields()
    {
        $response = $this->post(route('register'));
        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    public function testAdvertCanRegister()
    {
        Notification::fake();

        $data = [
            'name' => 'test',
            'domain' => 'test',
            'email' => 'test@test.ru',
            'phone' => '+79999999999',
            'policy' => true,
        ];

        $tech_domain = mb_strtolower($data['domain']) . '.' . config('app.domain');

        $response = $this->post(route('register'), $data);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('https://' . $tech_domain . '/?success=pp.created');

        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'name' => $data['name'],
            'role' => 'advertiser',
            'phone' => $data['phone'],
        ]);

        $this->assertDatabaseHas('pp', [
            'tech_domain' => $tech_domain,
        ]);

        // Проверяем, что письмо ушло
        $user = User::where('email', '=', $data['email'])->first();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function testAdvertCanNotRegisterWithAlreadyExistingEmail()
    {
        $user = factory(User::class)->create(['role' => 'advertiser',]);
        $data = [
            'role' => 'advertiser',
            'name' => 'test',
            'domain' => 'test',
            'email' => $user->email,
            'phone' => '+79999999999',
        ];

        $response = $this->post(route('register'), $data);
        $response->assertRedirect('/');

        $this->assertTrue(session()->hasOldInput('email'));
        $response->assertSessionHasErrors();
        $error = $response
            ->baseResponse
            ->getSession()
            ->get('errors')->getBag('default')
            ->get('email')[0];
        $this->assertSame($error, __('register-advert.fields.email.errors.exists'));
    }

    public function testAdvertCanNotRegisterWithAlreadyExistingPpDomain()
    {
        $user = factory(User::class)->create(['role' => 'advertiser',]);
        $pp = factory(Pp::class)->create([
            'user_id' => $user->id,
        ]);
        $data = [
            'role' => 'advertiser',
            'name' => 'test',
            'domain' => explode(".", $pp->tech_domain)[0],
            'email' => 'test@test.ru',
            'phone' => '+79999999999',
        ];

        $response = $this->post(route('register'), $data);
        $response->assertRedirect('/');

        $response->assertSessionHasErrors();
        $error = $response
            ->baseResponse
            ->getSession()
            ->get('errors')->getBag('default')
            ->get('domain')[0];
        $this->assertSame($error, __('register-advert.fields.domain.errors.exists'));
    }
}
