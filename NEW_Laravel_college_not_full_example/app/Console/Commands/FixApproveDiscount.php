<?php

namespace App\Console\Commands;

use App\DiscountStudent;
use App\Profiles;
use Illuminate\Console\Command;

class FixApproveDiscount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:approve:discount';

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
        $list = DiscountStudent::select(['discount_student.*', 'discount_type_list.discount as discount'])
            ->leftJoin('profiles', 'profiles.user_id', 'discount_student.user_id')
            ->leftJoin('users', 'users.id', 'discount_student.user_id')
            ->leftJoin('discount_type_list', 'discount_type_list.id', 'discount_student.type_id')
            ->where('discount_student.status', 'approved')
            ->whereNull('profiles.discount')
            ->whereNull('users.deleted_at')
            ->get();

        $i = 0;

        foreach ($list as $item)
        {
            $profile = Profiles::where('user_id', $item->user_id)->first();
            if($profile)
            {
                $profile->discount = $item->discount;
                $profile->save();
                $i++;
            }
        }

        $this->info($i);

        /*
         select discount_student.* from discount_student
left join profiles on profiles.user_id = discount_student.user_id
left join users on users.id = discount_student.user_id
where discount_student.status = 'approved'
and profiles.discount is NULL
and users.deleted_at is null
         * */
    }
}
