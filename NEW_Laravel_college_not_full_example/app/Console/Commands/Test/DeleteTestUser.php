<?php

namespace App\Console\Commands\Test;

use App\Profiles;
use App\User;
use App\UserRole;
use Illuminate\Console\Command;

class DeleteTestUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user:delete {--count=0}';

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
        $count = $this->option('count');

        if(!$count)
        {
            $this->error('Count not found');
            return;
        }

        $email = 'auto_test_#i@mail.ru';
        $userList = [];

        for($i = 1; $i <= $count; $i++)
        {
            $emailUser = str_replace('#i', $i, $email);

            $user = User::where('email', $emailUser)->first();

            if(!$user)
            {
                continue;
            }

            Profiles::where('user_id', $user->id)->delete();
            UserRole::where('user_id', $user->id)->delete();
            $user->delete();
        }
    }
}
