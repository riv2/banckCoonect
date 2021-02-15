<?php

namespace App\Console\Commands\Fix;

use App\Profiles;
use App\Services\StudentRating;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class GiveBuyAllow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'give:buy_allow';

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
                /** @var $profile Profiles */
                $year = $profile->speciality->year ?? null;

                if ($year == 2018 || $year == 2017) {
                    $boughtCredits = StudentDiscipline::where('student_id', $profile->user_id)
                        ->where('payed_credits', '>', 0)
                        ->exists();

                    if ($boughtCredits) {
                        $profile->buying_allow = 1;
                        $profile->save();
                    }
                }
            }
        });
    }
}
