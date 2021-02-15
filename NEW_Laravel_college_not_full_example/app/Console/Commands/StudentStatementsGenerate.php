<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\GenerateStudentContract;
use App\MgApplications;
use App\Order;
use App\OrderName;
use App\User;

class StudentStatementsGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statements:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate MG stundents enter documents';

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
        $allMg = MgApplications::get();

        foreach ($allMg as $mg) {
            $order = Order::getUserOrderNumber($mg->user_id , OrderName::ORDER_CODE_ENTER);

            if(isset($order)) {
                $this->info('user_id ' . $mg->user_id . ' orderid: '. $order->id);
                
                $user = User::where('id', $mg->user_id)->first();
                if(isset($user)) {
                    GenerateStudentContract::saveEducationStatement($mg->user_id);
                }
            }
        }
        
        $this->info('done');



        



    }

}

