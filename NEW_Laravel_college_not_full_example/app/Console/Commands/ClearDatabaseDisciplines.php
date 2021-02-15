<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDatabaseDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:db:disciplines';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('syllabus_quize_questions')->delete();

        DB::table('quize_answers')
            ->leftJoin(
                'quize_questions',
                'quize_questions.id',
                '=',
                'quize_answers.question_id')
            ->leftJoin(
                'entrance_test_quize',
                'entrance_test_quize.quize_question_id',
                '=',
                'quize_questions.id')
            ->whereNull('entrance_test_quize.id')
            ->delete();

        DB::table('quize_questions')
            ->leftJoin(
                'entrance_test_quize',
                'entrance_test_quize.quize_question_id',
                '=',
                'quize_questions.id')
            ->whereNull('entrance_test_quize.id')
            ->delete();

        DB::table('syllabus_document')->delete();
        DB::table('syllabus')->delete();

        DB::table('chatter_category_discipline')->delete();
        DB::table('chatter_categories')->delete();
        DB::table('chatter_post')->delete();
        DB::table('chatter_user_discussion')->delete();
        DB::table('chatter_post')->delete();
        DB::table('chatter_discussion')->delete();

        DB::table('discipline_full_accord')->delete();
        DB::table('course_discipline')->delete();
        DB::table('students_disciplines')->delete();
        DB::table('speciality_discipline')->delete();
        DB::table('disciplines')->delete();
    }
}
