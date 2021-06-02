<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValuesToNotifyParams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_params', function (Blueprint $table) {
            $table->string('gross_amount')->nullable()->after('method');
            $table->string('amount')->nullable()->after('method');
            $table->string('status')->nullable()->after('method');
            $table->string('order_id')->nullable()->after('method');
            $table->string('web_id')->nullable()->change();
            $table->string('click_id')->nullable()->change();
            $table->dropColumn('status_transaction_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notify_params', function (Blueprint $table) {
            $table->dropColumn('gross_amount');
            $table->dropColumn('amount');
            $table->dropColumn('status');
            $table->dropColumn('order_id');
            $table->string('status_transaction_value')->default()->after('status_reject_value');
        });
    }
}
