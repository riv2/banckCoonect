<?php

use Illuminate\Database\Seeder;

class AddFinanceNomenclature6353 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('finance_nomenclatures')->insert([
            'code'          => '00000006353',
            'name'          => 'Консультации по подготовке к Государственным экзаменам и написанию  дипломной работы/магистерской диссертации (стоимость 1 часа сверх утвержденных норм времени)',
            'name_kz'       => 'Мемлекеттік емтихандарға дайындық және дипломдық жұмыс / магистрлік диссертация жазу бойынша кеңес беру (бекітілген уақыттық нормалардан артық 1 сағат құны)',
            'type'          => \App\FinanceNomenclature::TYPE_FEE,
            'cost'          => 5000,
            'hidden'        => 1,
            'or_hidden'     => 0,
            'created_at'    => DB::raw('now()'),
            'updated_at'    => DB::raw('now()'),
        ]);
    }
}
