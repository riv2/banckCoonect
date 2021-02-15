<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NobdTrainedQuotaCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('nobd_trained_quota')) {
            Schema::create('nobd_trained_quota', function (Blueprint $table) {

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
        Schema::dropIfExists('nobd_trained_quota');
    }
}
