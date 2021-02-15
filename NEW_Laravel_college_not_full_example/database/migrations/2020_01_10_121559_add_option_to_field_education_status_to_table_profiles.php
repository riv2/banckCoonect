<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionToFieldEducationStatusToTableProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        DB::statement("
            ALTER TABLE `profiles` MODIFY COLUMN `education_status` 
            enum('matriculant','student','send_down','academic_leave','pregraduate','graduate','temp_suspended') NULL DEFAULT NULL;
        ");
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE `profiles` MODIFY COLUMN `education_status` 
            enum('matriculant','student','send_down','academic_leave','pregraduate','graduate') NULL DEFAULT NULL;
        ");
    }
}
