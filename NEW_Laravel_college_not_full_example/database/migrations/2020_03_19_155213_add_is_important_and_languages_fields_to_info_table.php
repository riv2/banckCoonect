<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsImportantAndLanguagesFieldsToInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('info', function (Blueprint $table) {
            $table->renameColumn('title', 'title_ru');
            $table->renameColumn('text', 'text_ru');

            $table->string('title_kz')->after('title');
            $table->string('title_en')->after('title_kz');

            $table->text('text_preview_ru')->after('title_en');
            $table->text('text_preview_kz')->after('text_preview_ru');
            $table->text('text_preview_en')->after('text_preview_kz');

            $table->mediumText('text_kz')->after('text');
            $table->mediumText('text_en')->after('text_kz');

            $table->boolean('is_important')->after('text_en');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('info', function (Blueprint $table) {
            $table->renameColumn('title_ru', 'title');
            $table->renameColumn('text_ru', 'text');

            $table->dropColumn('title_kz');
            $table->dropColumn('title_en');

            $table->dropColumn('text_preview_ru');
            $table->dropColumn('text_preview_kz');
            $table->dropColumn('text_preview_en');

            $table->dropColumn('text_kz');
            $table->dropColumn('text_en');

            $table->dropColumn('is_important');
        });
    }
}
