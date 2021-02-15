<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteLangFromDisciplines extends Migration
{
    public function up()
    {
        Schema::table(
            'disciplines',
            function (Blueprint $table) {
                $table->dropColumn('lang');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'disciplines',
            function (Blueprint $table) {
                $table->string('lang')->nullable()->after('description_kz');
            }
        );
    }
}