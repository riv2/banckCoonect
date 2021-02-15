<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFinishDateToDebtTrustTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('debt_trusts', function (Blueprint $table) {
            $table->date('finish_date')->nullable()->after('contract_current_debt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('debt_trusts', function (Blueprint $table) {
            $table->dropColumn('finish_date');
        });
    }
}
