<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayDocumentsLecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_documents_lectures', function (Blueprint $table) {
            $table->unsignedInteger('pay_document_id');
            $table->foreign('pay_document_id')->references('id')->on('pay_documents');

            $table->unsignedInteger('lecture_id');
            $table->foreign('lecture_id')->references('id')->on('lectures');

            $table->timestamps();
            $table->softDeletes();

            $table->primary([
                'pay_document_id',
                'lecture_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pay_documents_lectures');
    }
}
