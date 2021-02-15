<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:students';

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
        $students = User
            ::select('users.id as id')
            ->leftJoin('user_role', 'user_role.user_id', '=', 'users.id')
            ->where('user_role.role_id', 2)
            ->whereNotIn('email', [
                'i@thergbstudio.com',
                'dinarakapbarkyzy@yandex.kz',
                'bekkaraulan@yandex.kz',
                'turusbekova.elmira@yandex.ru',
                'irismatovadiyora@yandex.ru',
                'sandughashqanat@yandex.kz',
                'milenaishmetova@yandex.kz',
                'shukibairysbek@yandex.kz',
                'marzhanazimova@yandex.kz',
                'botaevayulduz@yandex.kz',
                'daniarbekmachkovsky@yandex.kz',
                'madina.eszhanova@yandex.ru'
            ])
            ->get();

        if($students) {
            $studentList = [];

            foreach ($students as $student) {
                $studentList[] = $student->id;
            }

            DB::table('user_role')->
            whereIn('user_id', $studentList)->delete();

            DB::table('pay_documents')->whereIn('user_id', $studentList)->delete();
            DB::table('student_lecture')->whereIn('user_id', $studentList)->delete();
            DB::table('lecture_rating')->whereIn('user_id', $studentList)->delete();
            DB::table('student_gpi')->whereIn('user_id', $studentList)->delete();
            DB::table('student_language_level')->whereIn('user_id', $studentList)->delete();

            DB::table('bc_applications')->whereIn('user_id', $studentList)->delete();
            DB::table('mg_applications')->whereIn('user_id', $studentList)->delete();
            DB::table('profiles')->whereIn('user_id', $studentList)->delete();

            DB::table('chatter_ban_user')->whereIn('user_id', $studentList)->delete();
            DB::table('help_requests')->whereIn('user_id', $studentList)->delete();
            DB::table('user_education_documents')->whereIn('user_id', $studentList)->delete();
            DB::table('promotion_user')->whereIn('user_id', $studentList)->delete();
            DB::table('students_disciplines')->whereIn('student_id', $studentList)->delete();
            DB::table('phone_confirm')->whereIn('user_id', $studentList)->delete();
            DB::table('users')->whereIn('id', $studentList)->delete();


        }

        $this->comment('Delete users. Count: ' . count($students));
    }
}
