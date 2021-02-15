<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserEducationDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_education_documents', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id');
            $table->enum('level', ['secondary', 'secondary_special', 'higher']);
            $table->string('doc_number');
            $table->string('doc_series');
            $table->string('institution_name');
            $table->date('date');
            $table->string('city')->nullable();
            $table->string('supplement_file_name')->comment('Имя файла приложения диплома');
            $table->string('speciality')->nullable()->comment('Специальность');
            $table->string('degree')->nullable()->comment('Степень');
            $table->boolean('kz_holder')->comment('1 - выдан в Казахстане 0 - нет');
            $table->string('institution_type')->nullable();
            $table->string('specialization')->nullable()->comment('Специализация');
            $table->string('nostrification')->nullable();
            $table->string('nostrification_file_name')->nullable();

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
        Schema::dropIfExists('user_education_documents');
    }
}
