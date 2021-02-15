<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionUserWorkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_user_work', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('promotion_user_id');
            $table->foreign('promotion_user_id')->references('id')->on('promotion_user');

            $table->string('work_certificate_file');
            $table->string('pension_report_file');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('promotion_user_work');
    }
}
