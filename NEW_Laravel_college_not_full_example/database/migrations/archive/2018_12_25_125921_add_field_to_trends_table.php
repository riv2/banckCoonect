<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldToTrendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->string('education_area_code')->after('name')
                ->comment('Код и классификация области образования');

            $table->string('training_code')->after('education_area_code')
                ->comment('Код и классификация направления подготовки');

            $table->string('op_code')->after('training_code')->comment('Код ОП');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trends', function (Blueprint $table) {
            $table->dropColumn('education_area_code');
            $table->dropColumn('training_code');
            $table->dropColumn('op_code');
        });
    }
}
