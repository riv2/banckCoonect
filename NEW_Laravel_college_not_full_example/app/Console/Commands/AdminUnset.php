<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Role;

class AdminUnset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:unset {--user=0}';

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
        $userId = (int)$this->option('user');

        $user = User::where('id', $userId)->first();

        if(!$user)
        {
            $this->error('User not found');
            return;
        }

        if($user->unsetRole(Role::NAME_ADMIN))
        {
            $this->info('Role admin removed');
        }
        else
        {
            $this->error('Role not remove');
        }
    }
}
