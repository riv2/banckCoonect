<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NobdTypeDirectionCreateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('nobd_type_direction')) {
            Schema::create('nobd_type_direction', function (Blueprint $table) {

                $table->increments('id');

                $table->string('code')->nullable(false);
                $table->string('name')->nullable(false);

                $table->timestamps();
                $table->softDeletes();

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
        Schema::dropIfExists('nobd_type_direction');
    }
}
