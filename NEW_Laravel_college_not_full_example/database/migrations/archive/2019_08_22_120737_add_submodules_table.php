<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubmodulesTable extends Migration
{
    public function __construct() {
        // Register ENUM type
        DB::getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submodules', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 260);
            $table->string('name_kz', 255);
            $table->string('name_en', 255);

            $table->unsignedInteger('ects');

            $table->string('dependence', 255);
            $table->string('dependence2', 255);
            $table->string('dependence3', 255);
            $table->string('dependence4', 255);
            $table->string('dependence5', 255);

            $table->timestamps();
        });

        Schema::create('module_submodule', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('submodule_id');
            $table->foreign('submodule_id')->references('id')->on('submodules')->onDelete('cascade');

            $table->unsignedInteger('module_id');
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('discipline_submodule', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines')->onDelete('cascade');

            $table->unsignedInteger('submodule_id');
            $table->foreign('submodule_id')->references('id')->on('submodules')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('speciality_submodule', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('speciality_id');
            $table->foreign('speciality_id')->references('id')->on('specialities');

            $table->unsignedInteger('submodule_id');
            $table->foreign('submodule_id')->references('id')->on('submodules');

            $table->enum('language_type', ['native', 'second', 'other'])->nullable()->default('native');

            $table->string('pressmark')->nullable()->comment('Шифр');
            $table->integer('semester')->nullable();
            $table->string('discipline_cicle',255)->nullable(true);
            $table->string('mt_tk',255)->nullable(true);

            $table->timestamps();
        });

        Schema::table('disciplines', function (Blueprint $table) {
            $table->unsignedInteger('ects')->nullable()->comment('кредиты европейские')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::drop('speciality_submodule');
        Schema::drop('discipline_submodule');
        Schema::drop('module_submodule');
        Schema::drop('submodules');

        Schema::table('disciplines', function (Blueprint $table) {
            $table->integer('ects')->nullable()->comment('кредиты европейские')->change();
        });
    }
}
