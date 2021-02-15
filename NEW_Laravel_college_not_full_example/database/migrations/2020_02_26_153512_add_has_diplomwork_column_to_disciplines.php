<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHasDiplomworkColumnToDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'disciplines',
            function (Blueprint $table) {
                $table->tinyInteger('has_diplomawork')->default(0)->after('is_practice');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('disciplines', 'has_diplomawork')) {
            Schema::table('disciplines', function (Blueprint $table)
            {
                $table->dropColumn('has_diplomawork');
            });
        }
    }
}
