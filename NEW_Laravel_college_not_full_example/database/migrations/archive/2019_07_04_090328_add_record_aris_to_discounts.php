<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRecordArisToDiscounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('discount_type_list')->insert([
            'category_id' => 3,
            'name_ru' => 'Для выпускников школ Арыса Туркестанской области',
            'name_kz' => 'Түркістан облысы Арыс мектеп бітірушілері үшін',
            'name_en' => 'For school graduates from Arys, Turkestan region',
            'discount' => 10
        ]);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('discount_type_list')
            ->where('name_en', 'For school graduates from Arys, Turkestan region')
            ->delete();
    }
}
