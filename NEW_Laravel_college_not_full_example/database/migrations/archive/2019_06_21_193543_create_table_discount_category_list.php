<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDiscountCategoryList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_category_list', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('name_en')->nullable();
            
            $table->timestamps();
        });

        DB::table('discount_category_list')->insert([
            'name' => 'Учебная',
            'name_kz' => 'Оқыту',
            'name_en' => 'Academic'
        ]);
        DB::table('discount_category_list')->insert([
            'name' => 'Социальная',
            'name_kz' => 'Әлеуметтік',
            'name_en' => 'Social'
        ]);
        DB::table('discount_category_list')->insert([
            'name' => 'Специальная',
            'name_kz' => 'Арнайы',
            'name_en' => 'Special'
        ]);
        DB::table('discount_category_list')->insert([
            'name' => 'Спортивная',
            'name_kz' => 'Спорт',
            'name_en' => 'Sport'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_category_list');
    }
}
