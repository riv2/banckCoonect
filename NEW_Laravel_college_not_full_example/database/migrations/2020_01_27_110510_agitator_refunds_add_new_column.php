<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgitatorRefundsAddNewColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasColumn('agitator_refunds', 'refunds_id')){
            Schema::table('agitator_refunds', function (Blueprint $table)
            {
                $table->dropForeign(['refunds_id']);
                $table->dropColumn('refunds_id');
            });
        }

        Schema::table('agitator_refunds', function (Blueprint $table) {
            $table->integer('percent')->after('cost');
            $table->string('order_number',255)->nullable(true)->after('percent');
            $table->enum('status',[
                'process',
                'success',
                'cancelled',
                'error'
            ])->nullable(true)->after('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agitator_refunds', function (Blueprint $table) {
            $table->dropColumn('percent');
            $table->dropColumn('order_number');
            $table->dropColumn('status');
        });
    }
}
