<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MgApplicationsAddKtColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->integer('kt_total')->nullable(true);
            $table->string('kt_name_1',255)->nullable(true);
            $table->integer('kt_val_1')->nullable(true);
            $table->string('kt_name_2',255)->nullable(true);
            $table->integer('kt_val_2')->nullable(true);
            $table->string('kt_name_3',255)->nullable(true);
            $table->integer('kt_val_3')->nullable(true);
            $table->string('kt_name_4',255)->nullable(true);
            $table->integer('kt_val_4')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mg_applications', function (Blueprint $table) {
            $table->dropColumn('kt_total');
            $table->dropColumn('kt_name_1');
            $table->dropColumn('kt_val_1');
            $table->dropColumn('kt_name_2');
            $table->dropColumn('kt_val_2');
            $table->dropColumn('kt_name_3');
            $table->dropColumn('kt_val_3');
            $table->dropColumn('kt_name_4');
            $table->dropColumn('kt_val_4');
        });
    }
}
