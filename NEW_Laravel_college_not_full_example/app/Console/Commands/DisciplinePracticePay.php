<?php

namespace App\Console\Commands;

use App\DisciplinesPracticePay;
use App\SyllabusTaskCoursePay;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Log};

class DisciplinePracticePay extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'discipline:practice:pay';

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
        $disciplinesPracticePay = DisciplinesPracticePay::
                                where('status', DisciplinesPracticePay::STATUS_PROCESS)->
                                whereRaw('ABS(TIMESTAMPDIFF(HOUR, created_at, ?)) >= 10', [ date('Y-m-d H:i:s', time()) ])->  //  часы
                                //whereRaw('ABS(TIMESTAMPDIFF(MINUTE, created_at, ?)) >= 10', [ date('Y-m-d H:i:s', time()) ])->     //  минуты
                                orderBy('id','ASC')->
                                limit(500)->
                                get();

        if( !empty($disciplinesPracticePay) && (count($disciplinesPracticePay) > 0) )
        {
            Log::info('FIND: ' . count($disciplinesPracticePay));

            foreach( $disciplinesPracticePay as $itemSTCP )
            {
                // изменяем статус
                $itemSTCP->status = SyllabusTaskCoursePay::STATUS_OK;
                $itemSTCP->save();
            }
        } else {
            Log::info('FIND: 0');
        }
    }
}
