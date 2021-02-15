<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EntranceExamFiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('entrance_exam_files')) {
            Schema::create('entrance_exam_files', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('entrance_exam_id')->nullable(false);
                $table->integer('speciality_id')->nullable(true);

                $table->string('name',255)->nullable(false);

                $table->tinyInteger('user_show')->default(0);
                $table->tinyInteger('employee_show')->default(0);

                $table->enum('type', [
                    'manual',
                    'commission_structure',
                    'composition_appeal_commission',
                    'schedule',
                    'protocols_creative_exams',
                    'protocols_appeal_commission',
                    'report_exams'
                ])->default('manual');

                $table->timestamps();
                $table->softDeletes();

                //$table->foreign('entrance_exam_id')->references('id')->on('entrance_exam');
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
        Schema::dropIfExists('entrance_exam_files');
    }
}
