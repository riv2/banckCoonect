<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateRecordInDiscountType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('discount_type_list')->where('name_kz', 'Orphans')
            ->update([
            "name_en" => 'Orphans',
            "name_kz" => 'Жетім балалар'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('discount_type_list')->where('name_en', 'Orphans')
            ->update([
            "name_kz" => 'Orphans',
            "name_en" => 'Жетім балалар'
            ]);
    }
}
