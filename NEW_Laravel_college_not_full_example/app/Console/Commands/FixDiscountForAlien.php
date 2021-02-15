<?php

namespace App\Console\Commands;

use App\DiscountStudent;
use App\Profiles;
use Illuminate\Console\Command;

class FixDiscountForAlien extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:discount:alien';

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
        $profiles = Profiles
            ::with('user')
            ->whereHas('user')
            ->where('alien', 1)
            ->where('discount', 50)
            ->get();

        $not2019 = [];
        $updated = [];

        foreach ($profiles as $profile)
        {
            if(date('Y', strtotime($profile->user->created_at)) == '2019')
            {
                $relationCount = DiscountStudent
                    ::where('user_id', $profile->user_id)
                    ->where('type_id', 15)
                    ->count();

                if (!$relationCount) {
                    $relation = new DiscountStudent();
                    $relation->type_id = 15;
                    $relation->user_id = $profile->user_id;
                    $relation->status = 'approved';
                    $relation->comment = 'Одобрено';
                    $relation->date_approve = date('Y-m-d', time());
                    $relation->save();

                    $updated[] = $profile->user_id;
                }
            }
            else
            {
                $not2019[] = $profile->user_id;
            }
        }

        print_r($updated);
        print_r($not2019);

        $this->info('Updated count ' . count($updated));
    }
}
