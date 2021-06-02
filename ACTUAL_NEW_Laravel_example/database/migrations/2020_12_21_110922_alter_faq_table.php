<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFaqTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->foreignId('faq_category_id')->after('id')->nullable();
            $table->integer('position')->after('user_type')->default(0);
            $table->dropColumn(['block_name', 'user_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faq', function (Blueprint $table) {
            $table->dropColumn(['block_name', 'user_type']);
            $table->string('block_name');
            $table->string('user_type');
        });
    }
}
