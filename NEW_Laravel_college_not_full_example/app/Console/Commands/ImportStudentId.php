<?php

namespace App\Console\Commands;

use App\Profiles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportStudentId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:import:id';

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
        $updateCount = 0;

        Profiles::whereNotNull('iin')->chunk(1000, function($profiles) use(&$updateCount){

            foreach ($profiles as $profile)
            {
                $person = DB::connection('miras_full')->table('person')
                    ->where('iin', $profile->iin)->first();

                if($person)
                {
                    $profile->ex_id = $person->id;
                    $profile->save();
                    $updateCount++;
                }
            }
        });

        $this->info('Updated rows: ' . $updateCount);
    }
}
