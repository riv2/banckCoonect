<?php

use App\DiscountTypeList;
use Illuminate\Database\Seeder;

class AddDiscountTypeList1Seeder extends Seeder
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
        $oDiscountTypeList->name_ru = 'Победители международных предметных олимпиад';
        $oDiscountTypeList->name_kz = 'Халықаралық пән олимпиадаларының жеңімпаздары';
        $oDiscountTypeList->name_en = 'Winners of international subject Olympiads';
        $oDiscountTypeList->citizen = 1;
        $oDiscountTypeList->hidden  = 1;
        $oDiscountTypeList->discount = 100;
        $oDiscountTypeList->save();
    }
}
