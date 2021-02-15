<?php

use App\DiscountTypeList;
use Illuminate\Database\Seeder;

class AddDiscountTypeList2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $oDiscountTypeList = new DiscountTypeList();
        $oDiscountTypeList->category_id = 3;
        $oDiscountTypeList->name_ru = 'Комитет по делам молодежи';
        $oDiscountTypeList->name_kz = 'Жастар ісі жөніндегі Комитет';
        $oDiscountTypeList->name_en = 'The Committee on youth Affairs';
        $oDiscountTypeList->citizen = 1;
        $oDiscountTypeList->hidden  = 1;
        $oDiscountTypeList->discount = 10;
        $oDiscountTypeList->save();
    }
}
