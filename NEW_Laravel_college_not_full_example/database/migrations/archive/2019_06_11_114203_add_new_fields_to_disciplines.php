<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldsToDisciplines extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->boolean('polylang')->nullable()->after('language_level_id');
            $table->enum('cycle', [
                'ood',
                'bd',
                'pd',
                'nirm',
                'eirm',
                'ia'
            ])->nullable()->after('polylang');
            $table->enum('type', [
                'ok',
                'vk',
                'kv'
            ])->nullable()->after('cycle');
            $table->integer('lecture_hours')->nullable()->after('type');
            $table->integer('practical_hours')->nullable()->after('lecture_hours');
            $table->integer('laboratory_hours')->nullable()->after('practical_hours');
            $table->integer('sro_hours')->nullable()->after('laboratory_hours');
            $table->boolean('has_coursework')->nullable()->after('sro_hours');
            $table->enum('control_form', [
                'test',
                'write',
                'report',
                'score'
            ])->nullable()->after('has_coursework')->comment('Форма контроля (score - зачет)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('disciplines', function (Blueprint $table) {
            $table->dropColumn('polylang');
            $table->dropColumn('cycle');
            $table->dropColumn('type');
            $table->dropColumn('lecture_hours');
            $table->dropColumn('practical_hours');
            $table->dropColumn('laboratory_hours');
            $table->dropColumn('sro_hours');
            $table->dropColumn('has_coursework');
            $table->dropColumn('control_form');
        });
    }
}
