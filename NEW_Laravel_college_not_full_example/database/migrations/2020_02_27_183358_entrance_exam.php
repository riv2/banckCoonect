<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('entrance_exam')) {
            Schema::create('entrance_exam', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name',255)->nullable(false);

                $table->date('date_start')->nullable(true);
                $table->date('date_end')->nullable(true);
                $table->tinyInteger('date_active')->default(0);
                $table->tinyInteger('date_user_show')->default(0);
                $table->tinyInteger('date_employee_show')->default(0);

                $table->tinyInteger('nct_number_active')->default(0);
                $table->tinyInteger('nct_number_user_show')->default(0);
                $table->tinyInteger('nct_number_employee_show')->default(0);

                $table->integer('passing_point')->nullable(true);
                $table->tinyInteger('passing_point_active')->default(0);
                $table->tinyInteger('passing_point_user_show')->default(0);
                $table->tinyInteger('passing_point_employee_show')->default(0);

                $table->tinyInteger('exams_date_active')->default(0);
                $table->tinyInteger('exams_date_user_show')->default(0);
                $table->tinyInteger('exams_date_employee_show')->default(0);

                $table->integer('custom_checked')->nullable(true);
                $table->tinyInteger('custom_checked_active')->default(0);
                $table->tinyInteger('custom_checked_user_show')->default(0);

                $table->timestamps();
                $table->softDeletes();

                //$table->foreign('speciality_id')->references('id')->on('specialities');

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
        Schema::dropIfExists('entrance_exam');
    }
}
