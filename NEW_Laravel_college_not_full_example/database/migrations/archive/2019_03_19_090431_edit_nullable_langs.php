<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditNullableLangs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE quize_answers MODIFY answer_fr longtext NULL');
        DB::statement('ALTER TABLE quize_answers MODIFY answer_ar longtext NULL');
        DB::statement('ALTER TABLE quize_answers MODIFY answer_de longtext NULL');

        DB::statement('ALTER TABLE quize_questions MODIFY question_fr longtext NULL');
        DB::statement('ALTER TABLE quize_questions MODIFY question_ar longtext NULL');
        DB::statement('ALTER TABLE quize_questions MODIFY question_de longtext NULL');

        DB::statement('ALTER TABLE syllabus MODIFY theme_number_fr varchar(255) NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_number_ar varchar(255) NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_number_de varchar(255) NULL');

        DB::statement('ALTER TABLE syllabus MODIFY theme_name_fr varchar(255) NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_name_ar varchar(255) NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_name_de varchar(255) NULL');

        DB::statement('ALTER TABLE syllabus MODIFY literature_fr longtext NULL');
        DB::statement('ALTER TABLE syllabus MODIFY literature_ar longtext NULL');
        DB::statement('ALTER TABLE syllabus MODIFY literature_de longtext NULL');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE quize_answers MODIFY answer_fr longtext NOT NULL');
        DB::statement('ALTER TABLE quize_answers MODIFY answer_ar longtext NOT NULL');
        DB::statement('ALTER TABLE quize_answers MODIFY answer_de longtext NOT NULL');

        DB::statement('ALTER TABLE quize_questions MODIFY question_fr longtext NOT NULL');
        DB::statement('ALTER TABLE quize_questions MODIFY question_ar longtext NOT NULL');
        DB::statement('ALTER TABLE quize_questions MODIFY question_de longtext NOT NULL');

        DB::statement('ALTER TABLE syllabus MODIFY theme_number_fr varchar(255) NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_number_ar varchar(255) NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_number_de varchar(255) NOT NULL');

        DB::statement('ALTER TABLE syllabus MODIFY theme_name_fr varchar(255) NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_name_ar varchar(255) NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY theme_name_de varchar(255) NOT NULL');

        DB::statement('ALTER TABLE syllabus MODIFY literature_fr longtext NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY literature_ar longtext NOT NULL');
        DB::statement('ALTER TABLE syllabus MODIFY literature_de longtext NOT NULL');
    }
}
