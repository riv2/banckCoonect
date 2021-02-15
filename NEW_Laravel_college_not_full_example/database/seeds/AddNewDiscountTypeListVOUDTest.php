<?php

use App\{DiscountTypeList};
use Illuminate\Database\Seeder;

class AddNewDiscountTypeListVOUDTest extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oDiscountTypeList = new DiscountTypeList();
        $oDiscountTypeList->category_id = 1;
        $oDiscountTypeList->name_ru     = 'Высокие результаты ВОУД';
        $oDiscountTypeList->name_kz     = 'ОЖСБ жоғары нәтижелері';
        $oDiscountTypeList->name_en     = 'High results of VOUD';
        $oDiscountTypeList->citizen     = 1;
        $oDiscountTypeList->hidden      = 0;
        $oDiscountTypeList->discount    = 50;
        $oDiscountTypeList->save();
    }
}
