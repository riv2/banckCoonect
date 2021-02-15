<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPractiseDatesToDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->string('practise_1sem_control_start', 5)->nullable()->after('is_practice');
            $table->string('practise_1sem_control_end', 5)->nullable()->after('practise_1sem_control_start');
            $table->string('practise_2sem_control_start', 5)->nullable()->after('practise_1sem_control_end');
            $table->string('practise_2sem_control_end', 5)->nullable()->after('practise_2sem_control_start');
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
            $table->dropColumn('practise_1sem_control_start');
            $table->dropColumn('practise_1sem_control_end');
            $table->dropColumn('practise_2sem_control_start');
            $table->dropColumn('practise_2sem_control_end');
        });
    }
}
