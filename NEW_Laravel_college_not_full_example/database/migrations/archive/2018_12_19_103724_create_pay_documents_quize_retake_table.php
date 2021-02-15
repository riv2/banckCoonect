<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayDocumentsQuizeRetakeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_documents_quize_retake', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('pay_document_id');
            $table->foreign('pay_document_id')->references('id')->on('pay_documents')->onDelete('cascade');

            $table->integer('student_discipline_id');
            $table->foreign('student_discipline_id')->references('id')->on('students_disciplines')->onDelete('cascade');

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
        Schema::dropIfExists('pay_documents_quize_retake');
    }
}
