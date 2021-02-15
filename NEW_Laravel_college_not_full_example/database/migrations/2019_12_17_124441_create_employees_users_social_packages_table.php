<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesUsersSocialPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('employees_users_social_packages')) {
            Schema::create('employees_users_social_packages', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('employees_user_id');
                $table->decimal('gas', 8, 2)->nullable();
                $table->decimal('basket', 8, 2)->nullable();
                $table->decimal('medicines', 8, 2)->nullable();
                $table->decimal('cellular', 8, 2)->nullable();
                $table->boolean('food')->default(0);
                $table->decimal('taxi', 8, 2)->nullable();
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
        Schema::dropIfExists('employees_users_social_packages');
    }
}
