<?php

namespace App\Console\Commands;

use App\Role;
use App\User;
use Illuminate\Console\Command;

class AdminSet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:set {--user=0}';

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

        if($user->hasRole('admin'))
        {
            $this->info('Admin role already set');
            return;
        }

        if($user->setRole(Role::NAME_ADMIN))
        {
            $this->info('Role admin added');
        }
        else
        {
            $this->error('Role not set');
        }

    }
}
