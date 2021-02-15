<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class RenameTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('quize_result_answer_discipline')) {
            Schema::rename('quize_result_answer_discipline', 'quiz_result_answers');
        }
    }

    public function down()
    {
        if (Schema::hasTable('quiz_result_answers')) {
            Schema::rename('quiz_result_answers', 'quize_result_answer_discipline');
        }
    }
}