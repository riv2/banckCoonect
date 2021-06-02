<?php

use Illuminate\Database\Seeder;

class BillingGatewaysSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!DB::table('_billing_gateways')->where('uniq', 'MANUAL')->first()) {
            DB::table('_billing_gateways')->insert([
                [
                    'uniq'             => 'MANUAL',
                    'name'             => 'Ручной',
                    'uniqs_currencies' => 's:3:"RUB";',
                    'advanced'         => '---',
                    'settings'         => '---',
                    'date_created'     => '2020-07-09',
                    'date_updated'     => '2020-07-09',
                    'enabled'          => '1'
                ]
            ]);
        }

        if (!DB::table('_billing_gateways')->where('uniq', 'ROBOKASSA')->first()) {
            DB::table('_billing_gateways')->insert([
                [
                    'uniq'             => 'ROBOKASSA',
                    'name'             => 'Робокасса',
                    'uniqs_currencies' => 's:3:"RUB";',
                    'advanced'         => '---',
                    'settings'         => '---',
                    'date_created'     => '2020-07-09',
                    'date_updated'     => '2020-07-09',
                    'enabled'          => '1'
                ]
            ]);
        }

    }
}