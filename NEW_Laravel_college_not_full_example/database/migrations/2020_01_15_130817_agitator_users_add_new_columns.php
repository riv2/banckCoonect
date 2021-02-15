<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AgitatorUsersAddNewColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('agitator_users', function (Blueprint $table) {
            $table->integer('cost')->nullable(true);
            $table->enum('status',[
                'process',
                'ok',
                'error'
            ])->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agitator_users', function (Blueprint $table) {
            $table->dropColumn('cost');
            $table->dropColumn('status');
        });
    }
}
