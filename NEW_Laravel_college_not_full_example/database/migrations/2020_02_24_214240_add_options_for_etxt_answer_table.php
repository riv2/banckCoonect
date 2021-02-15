<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOptionsForEtxtAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('etxt_answer', function (Blueprint $table) {
            $table->integer('num_samples')->nullable();
            $table->integer('num_ref_per_sample')->nullable();
            $table->boolean('num_samples_per_document')->nullable();
            $table->string('compare_method')->nullable();
            $table->integer('num_words_i_shingle')->nullable();
            $table->boolean('ignore_citation')->nullable();
            $table->integer('uniqueness_threshold')->nullable();
            $table->boolean('self_uniq')->nullable();
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
            $table->dropColumn('num_samples');
            $table->dropColumn('num_ref_per_sample');
            $table->dropColumn('num_samples_per_document');
            $table->dropColumn('compare_method');
            $table->dropColumn('num_words_i_shingle');
            $table->dropColumn('ignore_Citation');
            $table->dropColumn('uniqueness_threshold');
            $table->dropColumn('self_uniq');
        });
    }
}
