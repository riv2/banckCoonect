<?php

use Illuminate\Database\Seeder;

class AddFinanceNomenclature831 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('finance_nomenclatures')->insert([
            'code'          => 'БК000000831',
            'name'          => 'Участие в СНПК',
            'name_kz'       => 'Участие в СНПК',
            'type'          => \App\FinanceNomenclature::TYPE_FEE,
            'cost'          => 10000,
            'hidden'        => 1,
            'or_hidden'     => 0,
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
