<?php

namespace App\Console\Commands;

use File;
use App\{
        User,
        Profiles,
        EmployeesUser,
        EmployeesUsersPosition
    };
use App\Teacher\ProfileTeacher;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class EmployeesTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'employees_transfer';

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
        $notFound = '';
        $transfer = Excel::load(public_path('employees_dump.xlsx'));
        $transfer = $transfer->toArray();
        foreach ($transfer as $value) {
            if($value['user_id'] != '#N/A'){
                if(User::where('id', intval($value['user_id']))->first()){
                    $employment = $value['type'] == 'Совместительство' ? 'совместительство' : 'основная';
                    $employment_form = $value['type'] == 'Совместительство' ? 'Сотрудник по совместительству' : 'Штатный сотрудник';
                    EmployeesUser::updateOrCreate(
                        [
                            'user_id'    => intval($value['user_id'])
                        ],
                        [
                            'status'     => 'сотрудник',
                            'created_at' => $value['start_date']
                        ]
                    );
                    EmployeesUsersPosition::updateOrCreate(
                        [
                            'user_id'         => intval($value['user_id']),
                            'position_id'     => intval($value['position_id']),
                        ],
                        [
                            'employment'      => $employment,
                            'employment_form' => $employment_form,
                            'price'           => intval($value['rate']),
                            'salary'          => intval($value['salary']),
                            'organization'    => $value['is_university_employee'],
                            'created_at'      => $value['start_date'],
                            'payroll_type'    => $value['salary_calculation_type']
                        ]
                    );
                } else {
                    $notFound .= intval($value['user_id']).' - '.$value['email_from_miras_app'].' |||| ';
                }
            }
        }

        dd('ok');
    }
}
