<?php

namespace App\Console\Commands\Fix;

use App\DiscountStudent;
use App\Profiles;
use App\Services\IinService;
use Illuminate\Console\Command;

class FixDiscount extends Command
{
    const FIELD_IIN = 0;
    const FIELD_DISCOUNT_ID = 3;
    const FIELD_DISCOUNT_EXPIRE = 4;
    const FIELD_DISCOUNT_VAL = 5;
    const FIELD_ACTION = 15;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:discount';

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
        $file = fopen(storage_path('import/fix_discount.csv'), 'r');
        $exceptionIinList = [
            '960217401063'
        ];
        $updatedInnList = [];
        $updatedIdList = [];
        $discountList = [];

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            if($row[self::FIELD_ACTION] != 'норм' && !in_array($row[self::FIELD_IIN], $exceptionIinList))
            {
                $iin = IinService::normalize($row[self::FIELD_IIN]);
                $discount = str_replace('%', '', $row[self::FIELD_DISCOUNT_VAL]);
                $discountId = $row[self::FIELD_DISCOUNT_ID];
                $profile = Profiles::select('profiles.*')->where('iin', $iin)
                    ->leftJoin('users', 'users.id', '=', 'profiles.user_id')
                    ->whereNull('users.deleted_at')
                    ->get();

                if($profile)
                {
                    if(count($profile) == 1)
                    {
                        $profile = $profile[0];

                        $discountStudent = DiscountStudent
                            ::where('user_id', $profile->user_id)
                            ->where('type_id', $discountId)
                            ->where('status', DiscountStudent::STATUS_APPROVED)
                            ->first();

                        if(!$discountStudent)
                        {
                            DiscountStudent
                                ::where('user_id', $profile->user_id)
                                ->where('status', DiscountStudent::STATUS_APPROVED)
                                ->update(['status' => DiscountStudent::STATUS_CANCELED]);

                            $newDiscountStudent = new DiscountStudent();
                            $newDiscountStudent->type_id = $discountId;
                            $newDiscountStudent->user_id = $profile->user_id;
                            $newDiscountStudent->status = DiscountStudent::STATUS_APPROVED;
                            $newDiscountStudent->valid_till = date('Y-m-d H:i:s', strtotime($row[self::FIELD_DISCOUNT_EXPIRE] . ' 21:00:00'));
                            $newDiscountStudent->save();

                            $this->info('Insert discount. Discount_id=' . $discountId . '; iin = ' . $iin);
                        }

                        $profile->discount = $discount;
                        $profile->save();
                        $updatedInnList[] = $profile->iin;
                        $updatedIdList[] = $profile->user_id;
                        $discountList[] = $discount;
                    }
                    else
                    {
                        $this->warn('Double IIN: ' . $iin);
                    }
                }
                else
                {
                    $this->warn('IIN not found: ' . $iin);
                }
            }
        }

        print_r($updatedInnList);
        print_r($updatedIdList);
        print_r($discountList);
    }
}
