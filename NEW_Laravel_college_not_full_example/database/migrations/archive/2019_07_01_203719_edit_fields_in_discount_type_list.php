<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditFieldsInDiscountTypeList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('discount_type_list')->where('id', 2)->update(["hidden" => 1]); //ENT
        DB::table('discount_type_list')->where('id', 3)->update(["hidden" => 1]); //GPA

        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Скидка настроенна администрацией',
            'name_kz' => 'Әкімшілік орнатқан дисконт',
            'name_en' => 'Discount set by administration',
            'hidden' => 1,
            'discount' => 0
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('discount_type_list')->where('id', 2)->update(["hidden" => 0]); //ENT
        DB::table('discount_type_list')->where('id', 3)->update(["hidden" => 0]); //GPA

        DB::table('discount_type_list')->where('name_ru', 'Скидка настроенна администрацией')->delete();
    }
}
