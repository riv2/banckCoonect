<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesUserPublicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_user_publications')) {
            Schema::create('employees_user_publications', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('user_id');
                $table->string('theme');
                $table->string('science_branch');
                $table->text('content');
                $table->date('publication_date');
                $table->string('publication_name');
                $table->string('info');
                $table->string('impact_factor');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employees_user_publications');
    }
}
