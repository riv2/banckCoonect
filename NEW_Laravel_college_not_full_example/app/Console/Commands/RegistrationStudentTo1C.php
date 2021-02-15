<?php

namespace App\Console\Commands;

use App\Profiles;
use App\Services\Service1C;
use App\User;
use Illuminate\Console\Command;

class RegistrationStudentTo1C extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:registration:1c';

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
        $userIdList = [
            15703,
            15704,
            15709,
            15716,
            15719,
            15707,
            15720,
            15401,
            15265,
            15745,
            15417,
            15587,
            15584,
            15658,
            15442,
            15649,
            15792,
            15788,
            15783,
            15781,
            15780,
            15643,
            15754,
            15752,
            15757,
            15763,
            15760
        ];

        $profileList = Profiles
            ::whereIn('user_id', $userIdList)
            ->get();

        foreach ($profileList as $profile)
        {
            $result = Service1C::registration(
                $profile->iin,
                $profile->fio,
                $profile->sex,
                $profile->bdate
            );

            $this->info($profile->id . ' - ' . ($result ? 'true' : 'false'));
        }
    }
}
