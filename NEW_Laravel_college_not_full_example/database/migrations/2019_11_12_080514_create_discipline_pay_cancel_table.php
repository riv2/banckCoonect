<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDisciplinePayCancelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('discipline_pay_cancel')) {
            Schema::create('discipline_pay_cancel', function (Blueprint $table) {
                $table->increments('id');

                $table->integer('discipline_id');
                $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');

                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

                $table->unsignedInteger('admin_id')->nullable();
                $table->foreign('admin_id')->references('id')->on('users')->onDelete('cascade');

                $table->enum('status', ['new', 'approve', 'decline']);
                $table->boolean('executed_1c')->default(false);
                $table->boolean('executed_miras')->default(false);
                $table->text('decline_reason')->nullable();

                $table->timestamps();
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
        Schema::dropIfExists('discipline_pay_cancel');
    }
}
