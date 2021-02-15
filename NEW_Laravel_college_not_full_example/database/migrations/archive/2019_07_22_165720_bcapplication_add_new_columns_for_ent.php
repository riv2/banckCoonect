<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BcapplicationAddNewColumnsForEnt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            $table->string('ent_name_1_copy',255)->nullable(true);
            $table->string('ent_name_2_copy',255)->nullable(true);
            $table->string('ent_name_3_copy',255)->nullable(true);
            $table->string('ent_name_4_copy',255)->nullable(true);
            $table->string('ent_name_5_copy',255)->nullable(true);
            $table->integer('ent_val_1_copy')->nullable(true);
            $table->integer('ent_val_2_copy')->nullable(true);
            $table->integer('ent_val_3_copy')->nullable(true);
            $table->integer('ent_val_4_copy')->nullable(true);
            $table->integer('ent_val_5_copy')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            $table->dropColumn('ent_name_1_copy');
            $table->dropColumn('ent_name_2_copy');
            $table->dropColumn('ent_name_3_copy');
            $table->dropColumn('ent_name_4_copy');
            $table->dropColumn('ent_name_5_copy');
            $table->dropColumn('ent_val_1_copy');
            $table->dropColumn('ent_val_2_copy');
            $table->dropColumn('ent_val_3_copy');
            $table->dropColumn('ent_val_4_copy');
            $table->dropColumn('ent_val_5_copy');
        });
    }
}
