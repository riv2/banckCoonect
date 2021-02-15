<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAcademicLeave extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `profiles` MODIFY COLUMN `education_status` ENUM(\'matriculant\', \'student\', \'send_down\', \'academic_leave\') NULL ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `profiles` MODIFY COLUMN `education_status` ENUM(\'matriculant\', \'student\', \'send_down\') NULL ');
    }
}
