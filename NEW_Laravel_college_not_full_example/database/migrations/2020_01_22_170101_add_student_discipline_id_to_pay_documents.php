<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddStudentDisciplineIdToPayDocuments extends Migration
{
    public function up()
    {
        Schema::table(
            'pay_documents',
            function (Blueprint $table) {
                $table->integer('student_discipline_id')->after('user_id')->nullable();
            }
        );

        DB::statement('
UPDATE `pay_documents` 
INNER JOIN `pay_documents_student_disciplines` ON `pay_documents_student_disciplines`.`pay_document_id` = `pay_documents`.`id`
SET `pay_documents`.`student_discipline_id` = `pay_documents_student_disciplines`.`student_discipline_id` 
');
    }

    public function down()
    {
        Schema::table(
            'pay_documents',
            function (Blueprint $table) {
                $table->dropColumn('student_discipline_id');
            }
        );
    }
}