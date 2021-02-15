<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveModuleNameFromDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn('module_name_ru');
            $table->dropColumn('module_name_kz');
            $table->dropColumn('module_name_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('module_name_ru')->nullable()->after('module_number');
            $table->string('module_name_kz')->nullable()->after('module_name_ru');
            $table->string('module_name_en')->nullable()->after('module_name_kz');
        });
    }
}
