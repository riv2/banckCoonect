<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class ImportAgitators extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:agitators';

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
        $file = fopen(storage_path('import/student_agitators.csv'), 'r');
        $userIdList = [];

        while($row = fgetcsv($file))
        {
            $fio = $row[1];
            $iin = $row[2];
            $agitatorName = $row[3];
            $agitatorPhone = $row[4];
            $user = null;

            if($iin) {
                $user = User
                    ::where('referral_source', 'At the invitation of the agitator')
                    ->whereNull('referral_name')
                    ->whereHas('studentProfile', function ($query) use ($iin) {
                        $query->where('iin', $iin);
                    })->first();
            }
            else
            {
                $user = User
                    ::where('referral_source', 'At the invitation of the agitator')
                    ->whereNull('referral_name')
                    ->whereHas('studentProfile', function ($query) use ($fio) {
                        $query->where('fio', $fio);
                    })->first();
            }

            if($user)
            {
                if($agitatorName != 'Ахметова Маржан Темирханкызы')
                {
                    $user->referral_name = $agitatorName . '(' . $agitatorPhone . ')';
                    $user->save();

                }
                $userIdList[] = $user->id;
            }
        }

        User::whereIn('id', $userIdList)->whereNull('referral_name')->update([
            'referral_name' => 'Ахметова Маржан Темирханкызы(+77763095577)'
        ]);

        $this->info('Updated user count: ' . count($userIdList));
    }
}
