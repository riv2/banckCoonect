<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:db';

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
        $userWhiteList = [1,12,96];

        DB::table('articles')->delete();
        DB::table('article_translations')->delete();

        DB::table('user_role')->
            whereNotIn('user_id', $userWhiteList)->
            orWhere('role_id', '!=', 1)->delete();

        DB::table('profile_teachers')->delete();
        DB::table('teachers_additional_info')->delete();
        DB::table('teachers_documents_personals')->delete();
        DB::table('teachers_skills')->delete();

        DB::table('pay_documents_lectures')->delete();
        DB::table('pay_documents_lecture_room')->delete();
        DB::table('pay_documents_student_disciplines')->delete();
        DB::table('pay_documents')->delete();

        DB::table('lectures')->delete();
        DB::table('lecture_rating')->delete();
        DB::table('course_discipline')->delete();
        DB::table('courses')->delete();
        DB::table('student_lecture')->delete();

        DB::table('bc_applications')->delete();
        DB::table('mg_applications')->delete();
        DB::table('profiles')->delete();

        DB::table('chatter_ban_user')->delete();
        DB::table('help_requests')->delete();
        DB::table('user_education_documents')->delete();
        DB::table('promotion_user_work')->delete();
        DB::table('promotion_user')->delete();
        DB::table('users')->whereNotIn('id', $userWhiteList)->delete();
    }
}
