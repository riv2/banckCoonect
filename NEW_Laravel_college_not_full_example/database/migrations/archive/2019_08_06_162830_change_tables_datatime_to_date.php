<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTablesDatatimeToDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE teachers_experience MODIFY COLUMN date_from DATE");
        DB::statement("ALTER TABLE teachers_experience MODIFY COLUMN date_to DATE");

        DB::statement("ALTER TABLE teachers_education MODIFY COLUMN date_from DATE");
        DB::statement("ALTER TABLE teachers_education MODIFY COLUMN date_to DATE");
        DB::statement("ALTER TABLE teachers_education MODIFY COLUMN protocol_date DATE");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
