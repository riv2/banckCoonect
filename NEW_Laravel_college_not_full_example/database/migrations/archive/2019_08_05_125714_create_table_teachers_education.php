<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTeachersEducation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers_education', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->enum('type', [                                                  // тип образования
                'bachelor',
                'specialist',
                'magistracy',
                'scientific_degree',
                'academic_status',
                'language_ability'.
                'additional_skill'
            ])->default('bachelor');
            $table->dateTime('date_from')->nullable(true);                          //
            $table->dateTime('date_to')->nullable(true);                            //
            $table->string('education_place',255)->nullable(true);          // место образования
            $table->string('qualification_awarded',255)->nullable(true);    // Присвоенная квалификация
            $table->unsignedInteger('speciality_id')->nullable(true);                               // Шифр и наименование специальности
            $table->string('nostrification',255)->nullable(true);           // Нострификация
            $table->unsignedInteger('academic_degree_id')->nullable(true);                          // Ученая степень
            $table->unsignedInteger('scientific_field_id')->nullable(true);                         // Область науки
            $table->string('dissertation_topic_1',255)->nullable(true);     // Тема диссертации
            $table->string('dissertation_topic_2',255)->nullable(true);     // Тема диссертации
            $table->string('protocol_number',255)->nullable(true);          // Номер протокола

            $table->enum('academic_title', [                                        // Ученое звание
                'no_title',
                'associate_professor',
                'professor',
            ])->default('no_title');

            $table->dateTime('protocol_date')->nullable(true);                      // Номер протокола
            $table->text('embership_academies')->nullable(true);                    // Членство в академиях
            $table->unsignedInteger('lang_id')->nullable(true);                                     // Выбор языка
            $table->unsignedInteger('lang_level_id')->nullable(true);                               // Уровень
            $table->text('data_input')->nullable(true);                             // Ввод данных

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('speciality_id')->references('id')->on('specialities');
            $table->foreign('lang_id')->references('id')->on('languages');
            $table->foreign('lang_level_id')->references('id')->on('languages_level');
            $table->foreign('academic_degree_id')->references('id')->on('academic_degree');
            $table->foreign('scientific_field_id')->references('id')->on('scientific_field');

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
        Schema::dropIfExists('teachers_education');
    }
}
