<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeleteCascadeChatter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chatter_category_discipline', function (Blueprint $table) {
            $table->dropForeign('chatter_category_discipline_chatter_category_id_foreign');
            $table->dropForeign('chatter_category_discipline_discipline_id_foreign');

            $table->foreign('chatter_category_id')
                ->references('id')
                ->on('chatter_categories')
                ->onDelete('cascade')
                ->update();
            $table->foreign('discipline_id')
                ->references('id')
                ->on('disciplines')
                ->onDelete('cascade')
                ->update();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chatter_category_discipline', function (Blueprint $table) {
            //
        });
    }
}
