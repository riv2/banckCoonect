<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeSizeStringBcApplicaton extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE bc_applications MODIFY residence_registration_photo varchar(255)');
        DB::statement('ALTER TABLE bc_applications MODIFY military_photo varchar(255)');
        DB::statement('ALTER TABLE bc_applications MODIFY r086_photo varchar(255)');
        DB::statement('ALTER TABLE bc_applications MODIFY r063_photo varchar(255)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE bc_applications MODIFY residence_registration_photo varchar(64)');
        DB::statement('ALTER TABLE bc_applications MODIFY military_photo varchar(64)');
        DB::statement('ALTER TABLE bc_applications MODIFY r086_photo varchar(64)');
        DB::statement('ALTER TABLE bc_applications MODIFY r063_photo varchar(64)');
    }
}
