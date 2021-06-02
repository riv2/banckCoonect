<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClickIdToNotifyParams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notify_params', function (Blueprint $table) {
            $table->string('click_id')->after('status_transaction_value')->nullable();
            $table->string('web_id')->after('status_transaction_value')->nullable();
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
            $table->dropColumn('click_id');
            $table->dropColumn('web_id');
        });
    }
}
