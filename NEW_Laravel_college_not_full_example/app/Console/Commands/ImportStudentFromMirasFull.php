<?php

namespace App\Console\Commands;

use App\Profiles;
use Illuminate\Console\Command;

class ImportStudentFromMirasFull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student:miras_full {--iin=0}';

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
        $iin = $this->option('iin');

        $profile = Profiles::where('iin', $iin)->first();

        if(!$profile)
        {
            $this->error('Profile not found');
            return;
        }

        if($profile->importFromMirasFull())
        {
            $profile->user->save();
            $profile->save();
        }
    }
}
