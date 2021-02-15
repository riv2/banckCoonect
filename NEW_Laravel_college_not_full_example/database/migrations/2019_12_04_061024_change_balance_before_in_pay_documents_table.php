<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBalanceBeforeInPayDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `pay_documents` CHANGE `balance_before` `balance_before` DOUBLE(12,2) NULL DEFAULT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `pay_documents` CHANGE `balance_before` `balance_before` DOUBLE(8,2)  NULL DEFAULT NULL;');
    }
}
