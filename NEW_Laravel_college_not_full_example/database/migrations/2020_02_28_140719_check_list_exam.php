<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CheckListExam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('check_list_exam')) {
            Schema::create('check_list_exam', function (Blueprint $table) {

                $table->increments('id');

                $table->integer('check_list_id')->nullable(false);
                $table->integer('entrance_exam_id')->nullable(false);

                $table->tinyInteger('checked')->default(0);
                $table->tinyInteger('is_sum')->default(0);

                $table->string('nct_number',255)->nullable(true);
                $table->string('nct_code',50)->nullable(true);

                $table->date('exams_date')->nullable(true);

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
        Schema::dropIfExists('check_list_exam');
    }
}
