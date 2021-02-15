<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Profiles;
use App\StudentDiscipline;

class FixEngSpeciality2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:eng:speciality2';

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
        $profileList = Profiles
            ::whereHas('user', function($query){
                $query->where('keycloak', true);
                $query->where('import_type', 'eng_test');
            })
            ->get();

        $this->info('All count: ' . count($profileList));

        $newCount = 0;
        foreach ($profileList as $profile)
        {
            StudentDiscipline
                ::where('discipline_id', 896)
                ->where('student_id', $profile->user_id)
                ->update([
                    'discipline_id' => 1063
                ]);
        }

        $this->info('Add new disciplines: ' . $newCount);
    }
}
