<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddHashToQuizeResult extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('quize_result', 'hash')) {
            Schema::table(
                'quize_result',
                function (Blueprint $table) {
                    $table->string('hash', 10)->after('student_discipline_id');
                }
            );
        }
    }

    public function down()
    {
        if (Schema::hasColumn('quize_result', 'hash')) {
            Schema::table(
                'quize_result',
                function (Blueprint $table) {
                    $table->dropColumn('hash');
                }
            );
        }
    }
}