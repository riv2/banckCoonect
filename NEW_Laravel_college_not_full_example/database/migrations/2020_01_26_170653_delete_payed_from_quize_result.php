<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeletePayedFromQuizeResult extends Migration
{
    public function up()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->dropColumn('payed');
            }
        );
    }

    public function down()
    {
        Schema::table(
            'quize_result',
            function (Blueprint $table) {
                $table->boolean('payed')->after('student_discipline_id')->default(0);
            }
        );
    }
}