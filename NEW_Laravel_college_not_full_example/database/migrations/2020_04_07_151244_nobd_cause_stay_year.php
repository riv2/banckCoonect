<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NobdCauseStayYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('nobd_cause_stay_year')) {
            Schema::create('nobd_cause_stay_year', function (Blueprint $table) {

                $table->increments('id');

                $table->string('code')->nullable(false);
                $table->string('name')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nobd_cause_stay_year');
    }
}