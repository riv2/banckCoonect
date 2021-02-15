<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeNullableBcApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bc_applications', function (Blueprint $table) {
            $table->text('numeducation')->nullable()->change();
            $table->text('sereducation')->nullable()->change();
            $table->text('nameeducation')->nullable()->change();
            $table->text('dateeducation')->nullable()->change();
            $table->text('kzornot')->nullable()->change();
            $table->text('atteducation_photo')->nullable()->change();
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
            //
        });
    }
}
