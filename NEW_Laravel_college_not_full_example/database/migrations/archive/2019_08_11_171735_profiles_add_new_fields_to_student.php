<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProfilesAddNewFieldsToStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profiles', function (Blueprint $table) {

            $table->enum('family_status', [
                'single',
                'marital'
            ])->nullable(true);
            $table->smallInteger('course')->nullable(true);
            $table->string('team',255)->nullable(true);
            $table->text('workplace')->nullable(true);
            $table->text('previous_document')->nullable(true);
            $table->smallInteger('with_honors')->default(0);
            $table->date('date_certificate')->nullable(true);
            $table->smallInteger('is_transfer')->default(0);
            $table->string('transfer_course',255)->nullable(true);
            $table->enum('transfer_study_form', [
                'fulltime',
                'online',
                'evening',
                'distant',
            ])->nullable(true);
            $table->string('transfer_specialty',255)->nullable(true);
            $table->string('transfer_university',255)->nullable(true);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn('family_status');
            $table->dropColumn('course');
            $table->dropColumn('team');
            $table->dropColumn('workplace');
            $table->dropColumn('previous_document');
            $table->dropColumn('with_honors');
            $table->dropColumn('date_certificate');
            $table->dropColumn('is_transfer');
            $table->dropColumn('transfer_course');
            $table->dropColumn('transfer_study_form');
            $table->dropColumn('transfer_specialty');
            $table->dropColumn('transfer_university');
        });
    }
}
