<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBcApplicationEntNameType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            $table->string('ent_name_1')->nullable()->change();
            $table->string('ent_name_2')->nullable()->change();
            $table->string('ent_name_3')->nullable()->change();
            $table->string('ent_name_4')->nullable()->change();
            $table->string('ent_name_5')->nullable()->change();
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
            $table->integer('ent_name_1')->nullable()->change();
            $table->integer('ent_name_2')->nullable()->change();
            $table->integer('ent_name_3')->nullable()->change();
            $table->integer('ent_name_4')->nullable()->change();
            $table->integer('ent_name_5')->nullable()->change();
        });
    }
}
