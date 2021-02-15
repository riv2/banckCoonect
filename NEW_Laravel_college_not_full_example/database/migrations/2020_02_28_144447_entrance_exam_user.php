<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('entrance_exam_user')) {
            Schema::create('entrance_exam_user', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('speciality_id')->nullable(false);
                $table->integer('entrance_exam_id')->nullable(false);
                $table->integer('user_id')->nullable(false);
                $table->integer('point')->nullable(true);

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
        Schema::dropIfExists('entrance_exam_user');
    }
}
