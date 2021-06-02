<?php

use App\Models\UsersPayMethods;
use App\User;
use Illuminate\Database\Seeder;

class UsersPayMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::query()->where("role", "=", 'partner')
            ->get()->toArray();

        foreach ($users as $user) {
            for ($i = 0; $i < 10; $i++) {
                UsersPayMethods::withoutEvents(function () use ($i, $user) {
                    $UsersPayMethods = new UsersPayMethods();
                    $UsersPayMethods->user_id = $user['id'];
                    $UsersPayMethods->pay_method_id = rand(1,3);
                    $UsersPayMethods->cc_type = 'visa';
                    $UsersPayMethods->cc_number = rand(1000000000,31000000000);
                    $UsersPayMethods->company_name = 'SPACE X';
                    $UsersPayMethods->created_at = now();
                    $UsersPayMethods->save();
                });
            }
        }
    }
}
