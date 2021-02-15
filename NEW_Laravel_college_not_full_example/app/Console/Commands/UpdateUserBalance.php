<?php

namespace App\Console\Commands;

use App\Profiles;
use App\Services\Service1C;
use App\User;
use Illuminate\Console\Command;

class UpdateUserBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:balance:update';

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
        $users = User
            ::select(['users.id as id', 'profiles.iin as iin'])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->whereNotNull('profiles.iin')
            //->where('users.balance', '<', 0)
            ->get();

        $this->output->progressStart(count($users));

        foreach ($users as $user)
        {
            $balance = Service1C::getBalance($user->iin);
            User::where('id', $user->id)->update(['balance' => $balance]);
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }
}
