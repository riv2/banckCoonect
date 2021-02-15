<?php

namespace App\Console\Commands;

use App\DiscountStudent;
use App\DiscountTypeList;
use App\Profiles;
use Illuminate\Console\Command;

class SelectDiscountBug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'select:discount:bug';

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
        $profiles = Profiles::select([
            'profiles.id as id',
            'profiles.user_id as user_id',
            'profiles.discount as discount'
        ])
            ->leftJoin('users', 'users.id', '=', 'profiles.user_id')
            ->whereNull('users.deleted_at')
            ->whereNotNull('discount')
            ->get();

        $allCount = 0;
        $doubleApprove = [];
        $notHasApprove = [];
        $notEqApprove = [];

        foreach ($profiles as $profile)
        {
            $discounts = DiscountStudent::where('user_id', $profile->user_id)->get();

            if($discounts)
            {
                $approved = [];

                foreach ($discounts as $discount)
                {
                    if($discount->status == DiscountStudent::STATUS_APPROVED)
                    {
                        $approved[] = DiscountTypeList::where('id', $discount->type_id)->first();
                    }
                }

                if(count($approved) > 1)
                {
                    $doubleApprove[] = $profile->user_id;
                }
                else
                {
                    $appDiscount = isset($approved[0]->discount) ? $approved[0]->discount : '-';

                    if(count($approved) == 0)
                    {
                        $notHasApprove[] = $profile->user_id;
                        $allCount++;
                    }
                    else
                    {
                        if($approved[0]->discount != $profile->discount)
                        {
                            $notEqApprove[] = $profile->user_id;
                            $allCount++;
                        }
                    }

                    //$this->info($profile->user_id . ' : ' . count($discounts) . ' : ' . count($approved) . ' : ' . $profile->discount . ' : ' . $appDiscount );
                }
            }
        }

        $this->info('doubleApprove count: ' . implode(',', $doubleApprove));
        $this->info('notHasApprove count: ' . implode(',', $notHasApprove));
        $this->info('notEqApprove count: ' . implode(',', $notEqApprove));

        $this->info('doubleApprove count: ' . count($doubleApprove));
        $this->info('notHasApprove count: ' . count($notHasApprove));
        $this->info('notEqApprove count: ' . count($notEqApprove));
    }
}
