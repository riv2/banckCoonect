<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStudentRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_requests', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->unsignedInteger('edit_id')->nullable();
            $table->foreign('edit_id')->references('id')->on('student_requests');

            $table->unsignedInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');

            $table->unsignedInteger('type_id');
            $table->foreign('type_id')->references('id')->on('student_request_types');

            $table->unsignedInteger('user_id_who_declined')->nullable();
            $table->foreign('user_id_who_declined')->references('id')->on('users');

            $table->unsignedInteger('doc_id')->nullable();
            $table->foreign('doc_id')->references('id')->on('profile_docs');

            $table->date('date')->nullable();

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
        Schema::dropIfExists('student_requests');
    }
}
