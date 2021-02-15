<?php

namespace App\Console\Commands;

use App\Services\Service1C;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StudentRegistration1c extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:registration:1c';

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
        //Log::useDailyFiles(storage_path('logs/student_registration_1c.log'));

        $usersQ = User::with('studentProfile')
            ->whereHas('studentProfile', function($query){
                $query->where('registration_step', 'finish');
            })
            ->where('users.created_at', '>=', '2019-01-01');

        $usersCount = $usersQ;
        $usersCount = $usersCount->count();
        $this->output->progressStart($usersCount);

        $usersQ->chunk(1000, function($users)
            {
                foreach ($users as $user)
                {
                    if(isset($user->studentProfile->iin) && isset($user->studentProfile->fio) && isset($user->studentProfile->bdate))
                    {
                        $res = Service1C::registration(
                            $user->studentProfile->iin,
                            $user->studentProfile->fio,
                            $user->studentProfile->sex,
                            $user->studentProfile->bdate
                        );

                        if(!$res)
                        {
                            Log::error('Failed registration', ['user_id' => $user->id, 'iin' => $user->studentProfile->iin]);
                            $this->warn('Failed registration = ' . $user->id . '. iin: ' . $user->studentProfile->iin);

                        }
                    }
                    else
                    {
                        $params = [];

                        if(!isset($user->studentProfile->iin))
                        {
                            $params[] = 'iin';
                        }

                        if(!isset($user->studentProfile->fio))
                        {
                            $params[] = 'fio';
                        }

                        if(!isset($user->studentProfile->bdate))
                        {
                            $params[] = 'bdate';
                        }

                        Log::error('Invalid user params', [
                            'user_id' => $user->id,
                            'params' => implode(', ', $params)
                        ]);
                        $this->warn('Invalid user params. user_id = ' . $user->id . '. Params: ' . implode(', ', $params));
                    }

                    $this->output->progressAdvance();
                }
            });

        $this->output->progressFinish();
    }
}
