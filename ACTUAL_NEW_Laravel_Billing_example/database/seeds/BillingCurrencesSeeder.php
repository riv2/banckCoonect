<?php

use Illuminate\Database\Seeder;

class BillingCurrencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        if (!DB::table('_billing_currences')->where('uniq', 'MANUAL')->first()) {
            DB::table('_billing_currences')->insert([
                [
                    'uniq'         => 'RUB',
                    'name'         => 'Рубль',
                    'ratio'        => '1',
                    'code'         => 'RUB',
                    'date_created' => '2020-07-09',
                    'date_updated' => '2020-07-09',
                ]
            ]);
        }

        if (!DB::table('_billing_currences')->where('uniq', 'MANUAL')->first()) {
            DB::table('_billing_currences')->insert([
                [
                    'uniq'         => 'USD',
                    'name'         => 'Доллар',
                    'ratio'        => '77',
                    'code'         => 'USD',
                    'date_created' => '2020-07-09',
                    'date_updated' => '2020-07-09',
                ]
            ]);
        }

    }
}