<?php

namespace App\Console\Commands;

use App\{
    EmployeesPosition,
    EmployeesDepartment
};
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentAndPositionTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'department_position_transfer';

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
        $transfer = Excel::load(public_path('departments_and_position.xlsx'));
        $transfer = $transfer->toArray();

        foreach($transfer as $record){
            EmployeesDepartment::updateOrCreate(
                [
                    'id' => intval($record['department_id'])
                ],
                [
                    'id' => intval($record['department_id']),
                    'name' => $record['department_name'],
                    'superviser' => intval($record['supervising_department_id'])
                ]
            );
            
            EmployeesPosition::updateOrCreate(
                [
                    'id' => intval($record['position_id'])
                ],
                [
                    'department_id' => intval($record['department_id']),
                    'name' => $record['position_name']
                ]
            );
        }
        
        dd('ok');
    }
}
