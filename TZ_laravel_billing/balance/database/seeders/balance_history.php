<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class balance_history extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $balance = 1;
        $userId  = 1;

        $seedBalanceHistory[] = [
            'value'      => 1,
            'balance'    => $balance,
            'user_id'    => $userId,
            'created_at' => date('Y-m-d h:i:s')
        ];

        for($i = 1; $i <= 15; $i++) {

            $value   = ($i > 10) ? -1 : 1;
            $balance += $value;

            $seedBalanceHistory[] = [
                'value'      => $value,
                'balance'    => $balance,
                'user_id'    => $userId,
                'created_at' => date('Y-m-d h:i:s')
            ];
        }

        DB::table('balance_history')->insert($seedBalanceHistory);
    }
}
