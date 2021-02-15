<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpecialityTrajectoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speciality_trajectory', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')
                ->references('id')->on('specialities')->onDelete('cascade');

            $table->unsignedInteger('trajectory_id');
            $table->foreign('trajectory_id')
                ->references('id')->on('trajectories')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('speciality_trajectory');
    }
}
