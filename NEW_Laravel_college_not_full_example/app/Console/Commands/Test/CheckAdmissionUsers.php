<?php

namespace App\Console\Commands\Test;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckAdmissionUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:users:check:admission';

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
        /*$admisUsers =  DB::connection('miras_full')
            ->table('student_admission_state_exam')
            ->select(['student.id', 's_users.login'])
            ->leftJoin('student', 'student.id', '=', 'student_admission_state_exam.student_id')
            ->leftJoin('s_user_person', 's_user_person.person_id', '=', 'student.person_id')
            ->leftJoin('s_users', 's_users.id', '=', 's_user_person.user_id')
            ->get();

        foreach ($admisUsers as $user)
        {
            if($user->login != ''){
                if(User
                        ::where('email', $user->login)
                        ->where('keycloak', true)
                        ->where('import_type', User::IMPORT_TYPE_GOS_TEST)
                        ->count() == 0) {
                   $this->warn($user->login);
                }
            }
            else
            {
                $this->error($user->id);
            }
        }*/

        $users = User
            ::where('keycloak', true)
            ->where('import_type', User::IMPORT_TYPE_GOS_TEST)
            ->get();

        $count = 0;
        foreach ($users as $user)
        {
            if($user->hasAcademDebt())
            {
                $student = DB::connection('miras_full')
                    ->table('s_users')
                    ->select(['student.id as id'])
                    ->leftJoin('s_user_person', 's_user_person.user_id', '=', 's_users.id')
                    ->leftJoin('student', 'student.person_id', '=', 's_user_person.person_id')
                    ->where('s_users.login', $user->email)
                    ->first();

                $this->warn('sudent.id = ' . ($student->id ?? '') . '; login = ' . $user->email);
                $count++;
            }
        }

        $this->info('Has debt users count: ' . $count);
    }
}
