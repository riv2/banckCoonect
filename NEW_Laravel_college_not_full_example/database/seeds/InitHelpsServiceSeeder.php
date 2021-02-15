<?php

use App\{FinanceNomenclature};
use Illuminate\Database\Seeder;

class InitHelpsServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('finance_nomenclatures')->
        where('code','00000003274')->
        update([
            'type' => FinanceNomenclature::TYPE_HELPS
        ]);

    }
}
