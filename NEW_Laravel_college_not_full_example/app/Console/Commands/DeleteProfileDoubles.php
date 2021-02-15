<?php

namespace App\Console\Commands;

use App\Profiles;
use App\User;
use Illuminate\Console\Command;

class DeleteProfileDoubles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profiles:doubles:delete';

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
        $users = User::whereHas('studentProfile')->get();

        $doubleCount = 0;
        foreach ($users as $user) {
            $profiles = Profiles::where('user_id', $user->id)->orderBy('id', 'asc')->get();

            if(count($profiles) > 1)
            {
                Profiles::where('user_id', $user->id)->where('id', '!=', $profiles[0]->id)->delete();
                $doubleCount++;
                /*foreach ($del as $itemDel)
                {
                    $delArr[$user->id][] = $itemDel->id;
                }*/
            }
        }

        $this->info('Double count: ' . $doubleCount);
    }
}
