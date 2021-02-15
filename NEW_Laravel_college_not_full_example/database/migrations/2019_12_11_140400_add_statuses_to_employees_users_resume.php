<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStatusesToEmployeesUsersResume extends Migration
{
    // public function __construct() {
    //     // Register ENUM type
    //     DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    // }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('employees_users_resumes');
        Schema::create('employees_users_resumes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('vacancy_id');
            $table->enum('status', ['pending', 'interview', 'need_requirements', 'ready_requirements', 'approved', 'declined', 'revision_candidate_requirements', 'revision_position_requirements']);
            $table->text('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_users_resumes');
        Schema::create('employees_users_resumes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('vacancy_id');
            $table->enum('status', ['pending', 'interview', 'need_requirements', 'ready_requirements', 'approved', 'declined']);
            $table->timestamps();
        });
    }
}
