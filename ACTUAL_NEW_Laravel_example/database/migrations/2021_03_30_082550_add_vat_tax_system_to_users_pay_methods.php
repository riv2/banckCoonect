<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVatTaxSystemToUsersPayMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users_pay_methods', function (Blueprint $table) {
            $values = [
                'OSN',
                'USN',
                'ENVD',
            ];
            $table->boolean('vat_tax')->nullable()->after('bank_correspondent_account');
            $table->enum('taxation_system', $values)->nullable()->after('vat_tax');
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
            $table->dropColumn('vat_tax');
            $table->dropColumn('taxation_system');
        });
    }
}
