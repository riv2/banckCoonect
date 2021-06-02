<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractDateToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_pay_methods', function (Blueprint $table) {
            $table->date('contract_date')->nullable()->after('company_inn');
            $table->string('contract_number')->nullable()->after('contract_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users_pay_methods', function (Blueprint $table) {
            $table->dropColumn('contract_date');
            $table->dropColumn('contract_number');
        });
    }
}
