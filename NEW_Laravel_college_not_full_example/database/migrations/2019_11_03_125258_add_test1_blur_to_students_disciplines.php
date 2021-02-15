<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTest1BlurToStudentsDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->boolean('test1_blur')->default(false)->after('test1_result_trial');
        });


        Schema::table('quize_result', function (Blueprint $table) {
            $table->boolean('blur')->default(false)->after('letter');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_disciplines', function (Blueprint $table) {
            $table->dropColumn('test1_blur');
        });

        Schema::table('quize_result', function (Blueprint $table) {
            $table->dropColumn('blur');
        });
    }
}
