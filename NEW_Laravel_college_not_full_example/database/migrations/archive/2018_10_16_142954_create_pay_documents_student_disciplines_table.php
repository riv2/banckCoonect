<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayDocumentsStudentDisciplinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('pay_documents_student_disciplines')) {
            Schema::create('pay_documents_student_disciplines', function (Blueprint $table) {
                $table->integer('pay_document_id');
                $table->integer('student_discipline_id');
                $table->timestamps();
                $table->softDeletes();

                $table->primary([
                    'pay_document_id',
                    'student_discipline_id'
                ], 'pay_documents_student_disciplines_primary');
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
        Schema::dropIfExists('pay_documents_student_disciplines');
    }
}
