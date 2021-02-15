<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrCodes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('qr_codes');

        Schema::create('qr_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('teacher_id');
            $table->integer('discipline_id');
            $table->string('code', 60);

            $table->timestamp('created_at');

            $table->index(['teacher_id', 'discipline_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('qr_codes');
    }
}
