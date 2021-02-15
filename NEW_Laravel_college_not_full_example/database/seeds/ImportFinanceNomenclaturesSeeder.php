<?php

use Illuminate\Database\Seeder;

use App\FinanceNomenclature;

class ImportFinanceNomenclaturesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oFinanceNomenclature           = new FinanceNomenclature();
        $oFinanceNomenclature->code     = '00000007550';
        $oFinanceNomenclature->name     = 'Оплата за восстановление логина и пароля для студентов иностранных вузов - партнеров';
        $oFinanceNomenclature->name_kz  = 'Шетелдік серіктес жоғары оқу орындарының студенттері үшін логин мен парольді қалпына келтіру үшін төлем';
        $oFinanceNomenclature->name_en  = 'Payment for recovery of login and password for students of foreign universities - partners';
        $oFinanceNomenclature->type     = FinanceNomenclature::TYPE_FEE;
        $oFinanceNomenclature->cost     = 1000;
        $oFinanceNomenclature->hidden   = FinanceNomenclature::STATUS_HIDDEN;
        $oFinanceNomenclature->save();

    }
}
