<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToPayDocumentsLecturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pay_documents_lectures', function (Blueprint $table) {
            $table->enum('type', ['online', 'offline'])->after('lecture_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return voido
     */
    public function down()
    {
        Schema::table('pay_documents_lectures', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
