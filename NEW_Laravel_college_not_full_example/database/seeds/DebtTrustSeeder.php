<?php

use Illuminate\Database\Seeder;

class DebtTrustSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('debt_trusts')->insert([
            [
                'user_id' => 15816,
                'contract_number' => '09/527',
                'contract_month_cost' => 5000,
                'contract_current_debt' => 55000,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'user_id' => 10808,
                'contract_number' => '09/538-1',
                'contract_month_cost' => 5000,
                'contract_current_debt' => 55000,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'user_id' => 11117,
                'contract_number' => '09/542',
                'contract_month_cost' => 5000,
                'contract_current_debt' => 60000,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'user_id' => 11167,
                'contract_number' => '09/543',
                'contract_month_cost' => 5000,
                'contract_current_debt' => 50000,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ],
            [
                'user_id' => 9944,
                'contract_number' => '09/678',
                'contract_month_cost' => 11000,
                'contract_current_debt' => 98000,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ]
        ]);
    }
}
