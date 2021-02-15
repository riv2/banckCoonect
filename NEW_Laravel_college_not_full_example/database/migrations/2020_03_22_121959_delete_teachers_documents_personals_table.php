<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class DeleteTeachersDocumentsPersonalsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('teachers_documents_personals');
    }

    public function down()
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
}