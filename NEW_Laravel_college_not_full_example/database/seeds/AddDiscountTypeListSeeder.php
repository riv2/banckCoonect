<?php

use App\DiscountTypeList;
use Illuminate\Database\Seeder;

class AddDiscountTypeListSeeder extends Seeder
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
        $oDiscountTypeList->name_ru = 'Грант Президента Мирас';
        $oDiscountTypeList->name_kz = '«Мирас» президентінің гранты';
        $oDiscountTypeList->name_en = 'Grant of President Miras';
        $oDiscountTypeList->citizen = 1;
        $oDiscountTypeList->hidden  = 1;
        $oDiscountTypeList->discount = 100;
        $oDiscountTypeList->save();
    }
}
