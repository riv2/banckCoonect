<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NobdUserParticipationCompetitions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('nobd_user_pc')) {
            Schema::create('nobd_user_pc', function (Blueprint $table) {

                $table->increments('id');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedInteger('nobd_user_id');
                $table->foreign('nobd_user_id')->references('id')->on('nobd_user')->onDelete('cascade');

                // 1
                $table->unsignedInteger('type_event')->nullable(true)->comment('Вид мероприятия');
                $table->foreign('type_event')->references('id')->on('nobd_type_event');

                // 2
                $table->unsignedInteger('type_direction')->nullable(true)->comment('Вид направления');
                $table->foreign('type_direction')->references('id')->on('nobd_type_direction');

                // 3
                $table->unsignedInteger('events')->nullable(true)->comment('Уровень мероприятия');
                $table->foreign('events')->references('id')->on('nobd_events');

                // 4
                $table->date('date_participation')->nullable(true)->comment('Дата участия');

                // 5
                $table->unsignedInteger('reward')->nullable(true)->comment('Награда');
                $table->foreign('reward')->references('id')->on('nobd_reward');


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
        Schema::dropIfExists('nobd_user_pc');
    }
}
