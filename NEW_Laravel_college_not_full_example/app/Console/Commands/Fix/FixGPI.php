<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use Illuminate\Console\Command;

class FixGPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:gpa';

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
        Profiles::chunk(1000, function($profiles) {
            foreach ($profiles as $profile) {
                /** @var Profiles $profile */
                if (!empty($profile->user)) {
                    $profile->user->updateGpa();
                }
            }
        });
    }
}
