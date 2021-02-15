<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeachersDocumentsPersonalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teachers_documents_personals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->references('id')->on('users');

            $table->string('number')->nullable()->default(null);
            $table->string('issue_date')->nullable()->default(null);
            $table->string('expire_date')->nullable()->default(null);
            $table->text('issue_authority')->nullable()->default(null);

            $table->softDeletes();
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
        Schema::dropIfExists('teachers_documents_personals');
    }
}
