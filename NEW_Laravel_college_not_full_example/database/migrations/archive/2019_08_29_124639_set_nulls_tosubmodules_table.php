<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetNullsTosubmodulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE submodules MODIFY name varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY name_kz varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY name_en varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY ects int NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence2 varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence3 varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence4 varchar(255) NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence5 varchar(255) NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE submodules MODIFY name varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY name_kz varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY name_en varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY ects int NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence2 varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence3 varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence4 varchar(255) NOT NULL');
        DB::statement('ALTER TABLE submodules MODIFY dependence5 varchar(255) NOT NULL');
    }
}
