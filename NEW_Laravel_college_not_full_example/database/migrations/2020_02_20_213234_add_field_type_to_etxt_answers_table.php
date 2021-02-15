<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldTypeToEtxtAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('etxt_answer', function (Blueprint $table) {
            $table->string('type')->default('text');
            $table->mediumText('text')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('etxt_answer', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->text('text')->change();
        });
    }
}
