<?php

namespace App\Console\Commands\Import;

use App\Order;
use App\OrderUser;
use App\Profiles;
use Illuminate\Console\Command;

class SetOrderUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:order:users';

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
        $file = fopen(storage_path('import/set_order_users.csv'), 'r');
        $fileReport = fopen(storage_path('import/set_order_users_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/set_order_users.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $userId = $row[0];
            $orderName = $row[11];

            $user = Profiles::where('user_id', $userId)->first();

            if($user)
            {
                $order = $this->getOrderByName($orderName);

                if($order)
                {
                    $orderUser = OrderUser
                        ::where('user_id', $user->user_id)
                        ->where('order_id', $order->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if(!$orderUser)
                    {
                        $order->attachUser($user->user_id);
                        $user = Profiles::where('user_id', $userId)->first();

                        if($user->education_status == Profiles::EDUCATION_STATUS_STUDENT)
                        {
                            $row[] = 'success';
                        }
                        else
                        {
                            $row[] = 'education_status not set';
                        }
                    }
                    else
                    {
                        $row[] = 'already in order';
                    }
                }
                else
                {
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
