<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;


class DefaultPasswordForStudents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:password:default {--user_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Password reset and generate default value';

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

        $userId = (int)$this->option('user_id');

        $userList = User::select('id')->whereHas('studentProfile');
        $defaultPass = 'Miras2019';

        if($userId !== 0)
        {
            $userList->where('id', $userId);
        }

        $userListForUpdate = $userList;
        $userListForUpdate->update([
            'password' => bcrypt($defaultPass),
            'keycloak' => false
        ]);

        $userList = $userList->get();

        $this->output->progressStart(count($userList));
        foreach ($userList as $user)
        {
            $user->setRole('client');
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
        $this->info('Update users count: ' . count($userList));

    }
}
