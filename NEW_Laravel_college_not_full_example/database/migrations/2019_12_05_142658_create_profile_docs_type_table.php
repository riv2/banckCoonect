<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfileDocsTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('profile_docs_type')) {
            Schema::create('profile_docs_type', function (Blueprint $table) {
                $table->increments('id');
                $table->timestamps();

                $table->string('type')->nullable();
                $table->string('group')->nullable();
                $table->string('folder')->nullable();
                $table->integer('hidden')->nullable();
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
        Schema::dropIfExists('profile_docs_type');
    }
}
