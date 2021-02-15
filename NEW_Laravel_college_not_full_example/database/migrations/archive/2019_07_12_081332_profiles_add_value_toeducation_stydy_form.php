<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfilesAddValueToeducationStydyForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE profiles MODIFY COLUMN education_study_form ENUM('fulltime','night','online','evening')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE profiles MODIFY COLUMN education_study_form ENUM('fulltime','night','online')");
    }
}
