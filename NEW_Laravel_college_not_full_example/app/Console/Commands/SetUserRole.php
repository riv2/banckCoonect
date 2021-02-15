<?php

namespace App\Console\Commands;

use App\Role;
use Illuminate\Console\Command;
use App\User;

class SetUserRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:role:set {--user=0} {--role=}';

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
        $userEmail = $this->option('user');
        $role = $this->option('role');

        $user = User::where('email', $userEmail)->first();
        $roleHas = (bool)Role::where('name', $role)->count();

        if(!$user)
        {
            $this->error('User not found');
            return;
        }

        if(!$roleHas)
        {
            $this->error('Role not found');
            return;
        }

        if($user->hasRole($role))
        {
            $this->info('Admin role already set');
            return;
        }

        if($user->setRole($role))
        {
            $this->info('Role '. $role .' added');
        }
        else
        {
            $this->error('Role not set');
        }
    }
}
