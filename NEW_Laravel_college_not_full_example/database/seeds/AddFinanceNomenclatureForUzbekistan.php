<?php

use App\FinanceNomenclature;
use Illuminate\Database\Seeder;

class AddFinanceNomenclatureForUzbekistan extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $oFinanceNomenclature = new FinanceNomenclature();
        $oFinanceNomenclature->code    = '00000007845';
        $oFinanceNomenclature->name    = 'Оформление документов обучающихся-нерезидентов РК УЗБЕКИСТАН';
        $oFinanceNomenclature->name_kz = 'ҚР бейрезидент-білім алушылардың құжаттарын рәсімдеу Өзбекстан';
        $oFinanceNomenclature->name_en = 'Registration of documents of students-non-residents of Kazakhstan UZBEKISTAN';
        $oFinanceNomenclature->type    = FinanceNomenclature::TYPE_FEE;
        $oFinanceNomenclature->cost    = 35000;
        $oFinanceNomenclature->hidden  = 1;
        $oFinanceNomenclature->save();

    }
}
