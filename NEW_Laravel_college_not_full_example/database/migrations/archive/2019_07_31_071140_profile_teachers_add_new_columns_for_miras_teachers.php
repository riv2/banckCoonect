<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfileTeachersAddNewColumnsForMirasTeachers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {

            $table->integer('nationality_id')->nullable(true);
            $table->integer('citizenship_id')->nullable(true);
            $table->enum('alien', [
                'resident',
                'alien'
            ])->default('resident');
            $table->enum('family_status', [
                'single',
                'marital'
            ])->default('marital');
            $table->string('docseries',255)->nullable(true);
            $table->date('expire_date')->nullable(true);
            $table->text('actual_address')->nullable(true);
            $table->text('home_address')->nullable(true);
            $table->string('home_phone',255)->nullable(true);
            $table->string('resume_link',255)->nullable(true);
            $table->string('registration_step',255)->nullable(true);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profile_teachers', function (Blueprint $table) {
            $table->dropColumn('nationality_id');
            $table->dropColumn('citizenship_id');
            $table->dropColumn('alien');
            $table->dropColumn('family_status');
            $table->dropColumn('docseries');
            $table->dropColumn('expire_date');
            $table->dropColumn('actual_address');
            $table->dropColumn('home_address');
            $table->dropColumn('home_phone');
            $table->dropColumn('resume_link');
            $table->dropColumn('registration_step');
        });
    }
}
