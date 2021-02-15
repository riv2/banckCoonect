<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBalanceBefore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_finance_nomenclature', function (Blueprint $table) {
            $table->float('balance_before')->after('semester')->comment('Balance before pay')->nullable();
        });

        Schema::table('pay_documents', function (Blueprint $table) {
            $table->float('balance_before')->after('amount')->comment('Balance before pay')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_finance_nomenclature', function (Blueprint $table) {
            $table->dropColumn('balance_before');
        });

        Schema::table('pay_documents', function (Blueprint $table) {
            $table->dropColumn('balance_before');
        });
    }
}
