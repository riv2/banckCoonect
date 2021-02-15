<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsDisciplinesElectiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('students_disciplines_elective')) {
            Schema::create('students_disciplines_elective', function (Blueprint $table) {
                $table->integer('id', true);

                $table->integer('discipline_id')->nullable();
                $table->integer('test_result')->nullable();
                $table->float('test_result_points')->nullable()->comment('Оценка в баллах');
                $table->float('test_result_gpi')->nullable()->comment('Оценка gpi');
                $table->string('test_result_letter')->nullable();
                $table->string('analogue')->nullable();
                $table->string('notes')->nullable();
                $table->integer('student_id')->nullable();
                $table->integer('payed')->nullable();
                $table->integer('payed_credits')->nullable();
                $table->integer('approved')->nullable();
                $table->integer('iteration')->default(1);
                $table->float('total_points')->nullable();
                $table->timestamps();
                $table->boolean('syllabus_updated')->default(false);
                $table->tinyInteger('migrated')->default(0);
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
        Schema::dropIfExists('students_disciplines_elective');
    }
}
