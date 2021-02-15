<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddHashToQuizRezult extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->string('hash', 10)->after('student_discipline_id');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->dropColumn('hash');
            }
        );
    }
}