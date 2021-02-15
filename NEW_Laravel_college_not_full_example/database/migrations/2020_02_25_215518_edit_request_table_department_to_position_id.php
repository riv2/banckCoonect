<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditRequestTableDepartmentToPositionId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_request_signs', function (Blueprint $table) {
            $table->renameColumn('department_id', 'position_id');
            $table->dropForeign('student_request_signs_department_id_foreign');
            $table->foreign('position_id')->references('id')->on('employees_positions');
        });

        Schema::table('student_request_type_signers', function (Blueprint $table) {
            $table->renameColumn('department_id', 'position_id');
            $table->dropForeign('student_request_type_signers_department_id_foreign');
            $table->foreign('position_id')->references('id')->on('employees_positions');
        });





        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_request_signs', function (Blueprint $table) {
            $table->renameColumn('position_id', 'department_id');
            $table->dropForeign('student_request_signs_position_id_foreign');
            $table->foreign('department_id')->references('id')->on('employees_departments');
        });

        Schema::table('student_request_type_signers', function (Blueprint $table) {
            $table->renameColumn('position_id', 'department_id');
            $table->dropForeign('student_request_type_signers_position_id_foreign');
            $table->foreign('department_id')->references('id')->on('employees_departments');
        });
    }
}
