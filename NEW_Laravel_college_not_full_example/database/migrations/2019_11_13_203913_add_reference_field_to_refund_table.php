<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReferenceFieldToRefundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('refunds_list', function (Blueprint $table) {
            $table->integer('doc_id')->nullable();
            $table->string('sms_key')->comment('в случае 1 зачит подтвержденно')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('refunds_list', function (Blueprint $table) {
            $table->dropColumn('doc_id');
            $table->dropColumn('sms_key');
        });
    }
}
