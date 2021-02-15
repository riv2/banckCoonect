<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentsDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('students_disciplines')) {
            Schema::create('students_disciplines', function (Blueprint $table) {
                $table->integer('id', true);

                $table->integer('discipline_id')->nullable();
                $table->integer('test_result')->nullable();
                $table->string('analogue')->nullable();
                $table->string('notes')->nullable();
                $table->integer('student_id')->nullable();
                $table->integer('payed')->nullable();
                $table->integer('approved')->nullable();

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
        Schema::dropIfExists('students_disciplines');
    }
}
