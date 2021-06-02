<?php

use App\Models\Click;
use App\Models\Link;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Pp;
use App\Models\PpPayMethod;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'id' => 1,
                'email' => 'admin@test.ru',
                'role' => 'admin',
                'pp_id' => null,
                'email_verified_at' => now(),
                'name' => 'admin',
                'password' => '123admin321',
            ],
            [
                'id' => 2,
                'email' => 'manager@test.ru',
                'role' => 'manager',
                'pp_id' => null,
                'email_verified_at' => now(),
                'name' => 'manager',
                'password' => '123manager321',
            ],
            [
                'id' => 3,
                'email' => 'advertiser@test.ru',
                'role' => 'advertiser',
                'pp_id' => 1,
                'email_verified_at' => now(),
                'name' => 'advertiser',
                'password' => '123advertiser321',
            ],
            [
                'id' => 4,
                'email' => 'partner@test.ru',
                'role' => 'partner',
                'pp_id' => 1,
                'email_verified_at' => now(),
                'name' => 'partner',
                'password' => '123partner321',
            ],
            [
                'id' => 5,
                'email' => 'analyst@test.ru',
                'role' => 'analyst',
                'pp_id' => 1,
                'email_verified_at' => now(),
                'name' => 'analyst',
                'password' => '123analyst321',
            ],
        ];
        foreach ($users as $item) {
            $u = User::query()->where('email', '=', $item['email'])->first();
            if ($u) {
                continue;
            }
            User::withoutEvents(function () use ($item) {
                $user = new User();
                $user->email = $item['email'];
                $user->role = $item['role'];
                $user->pp_id = $item['pp_id'];
                $user->email_verified_at = $item['email_verified_at'];
                $user->name = $item['name'];
                $user->password = Hash::make($item['password']);
                $user->save();
            });
        }


        $pp = Pp::query()->where('id', '=', 1)->first();
        if (!$pp) {
            $pp = new Pp();
        }
        $pp->id = 1;
        $pp->user_id = 3;
        $pp->tech_domain = 'test.' . config('app.domain');
        $pp->prod_domain = 'test.' . config('app.domain');
        $pp->short_name = 'Тестовая ПП';
        $pp->long_name = 'Партнерка лучше, чем у всех';
        $pp->onboarding_status = 'first_offer_added';
        $pp->company_url = 'https://google.com';
        $pp->pp_target = 'products';
        $pp->currency = 'RUB';
        $pp->logo = '/storage/logo/logo_1.png';
        $pp->lang = ['ru' => true, 'en' => true, 'es' => true];
        $pp->tariff = 'free';
        $pp->status = 'active';
        $pp->pay_methods()->attach(PpPayMethod::query()->get()->pluck('pay_method_id'));
        $pp->save();

        for ($i = 0; $i < 10; $i++) {
            $offer = factory(Offer::class)->make([
                'pp_id' => $pp->id,
                'user_id' => $pp->user_id,
            ]);
            $offer->save();
        }

        for ($i = 0; $i < 7; $i++) {
            $link = factory(Link::class)->make([
                'pp_id' => $pp->id,
                'partner_id' => 4,
                'offer_id' => $i+1,
            ]);
            $link->save();
        }
        for ($i = 0; $i < 100; $i++) {
            $click = factory(Click::class)->make([
                'pp_id' => $pp->id,
                'partner_id' => 4,
                'link_id' => Link::query()
                    ->where('pp_id', '=', $pp->id)
                    ->where('partner_id', '=', 4)
                    ->first()
                    ->id,
            ]);
            $click->save();
        }

        for ($i = 0; $i < 20; $i++) {
            $order = factory(Order::class)->make([
                'pp_id' => $pp->id,
                'partner_id' => 4,
            ]);
            $order->save();
        }
    }
}
