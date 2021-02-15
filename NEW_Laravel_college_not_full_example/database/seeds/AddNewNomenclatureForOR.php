<?php

use App\FinanceNomenclature;
use Illuminate\Database\Seeder;

class AddNewNomenclatureForOR extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oFinanceNomenclature = new FinanceNomenclature();
        $oFinanceNomenclature->code    = '00000006991';
        $oFinanceNomenclature->name    = 'Организация прохождения военной подготовки, оформление сопутствующей документации, 1 год';
        $oFinanceNomenclature->name_kz = 'Әскери даярлықтан өтуді ұйымдастыру, ілеспе құжаттарды ресімдеу, 1 жыл';
        $oFinanceNomenclature->name_en = 'Organization of military training, registration of related documentation, 1 year';
        $oFinanceNomenclature->type    = FinanceNomenclature::TYPE_FEE;
        $oFinanceNomenclature->cost    = 5000;
        $oFinanceNomenclature->hidden  = 1;
        $oFinanceNomenclature->save();
    }
}
