<?php

namespace App\Console\Commands\Fix;

use App\Order;
use App\OrderUser;
use App\Profiles;
use Illuminate\Console\Command;

class UnsetOrderUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'unset:order:users';

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
        $file = fopen(storage_path('import/unset_order_users.csv'), 'r');
        $fileReport = fopen(storage_path('import/unset_order_users_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/unset_order_users.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $profile = Profiles::where('user_id', $userId)->first();

            if($profile)
            {
                $order = Order
                    ::select(['orders.*', 'order_user.id as order_user_id'])
                    ->leftJoin('order_user', 'order_user.order_id', '=', 'orders.id')
                    ->where('order_user.user_id', $userId)
                    ->orderBy('order_user.created_at', 'desc')
                    ->first();

                if($order && $order->order_action_id == 12)
                {
                    $orderUser = OrderUser
                        ::where('id', $order->order_user_id)
                        ->first();

                    $orderUser->delete();
                    $profile->education_status = Profiles::EDUCATION_STATUS_STUDENT;
                    $profile->save();
                    $row[] = 'success';
                }
                else
                {
                    $profile->education_status = Profiles::EDUCATION_STATUS_STUDENT;
                    $profile->save();
                    $row[] = 'order not found';
                }
            }
            else
            {
                $row[] = 'user not found';
            }

            fputcsv($fileReport, $row);
            $this->output->progressAdvance();
        }

        fclose($fileReport);
        fclose($file);

        $this->output->progressFinish();
    }

    /**
     * @param $name
     * @return bool
     */
    public function getOrderByName($name)
    {
        $name = str_replace('№', '', $name);
        $name = trim($name);

        $name = explode(' ', $name);

        if(!empty($name[0]))
        {
            return Order::where('number', $name)->first();
        }

        return false;
    }
}
