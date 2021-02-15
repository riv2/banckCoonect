<?php

namespace App\Console\Commands;

use App\Speciality;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AttachUserDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attach:user:disciplines {--user=all} {--year=all}';

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
        $userId = $this->option('user');
        $year = $this->option('year');

        $userList = User
            ::with('studentProfile')
            ->whereHas('studentProfile', function($query){
                $query->where('registration_step', 'finish');
                $query->whereNotNull('education_speciality_id');
            });

        if($userId != 'all')
        {
            $userList = $userList->where('id', $userId);
        }

        if($year != 'all')
        {
            $userList = $userList->where('created_at', '>=', $year . '-01-01');
        }

        $userListCount = $userList;

        $this->output->progressStart($userListCount->count());

        $userList->chunk(1000, function($users){
            foreach ($users as $user) {
                $user->studentProfile->updateDisciplines();
                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();
    }
}
