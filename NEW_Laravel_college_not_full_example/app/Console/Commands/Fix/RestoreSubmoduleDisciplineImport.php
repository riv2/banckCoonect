<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;

class RestoreSubmoduleDisciplineImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:submodule:discipline:import';

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
        $file = fopen(storage_path('import/restore_submodule_discipline_report.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/restore_submodule_discipline_report.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $studentDiscipline = StudentDiscipline::where('id', $row[0])->first();

            $admin = null;
            if($row[58])
            {
                $admin = User::where('id', $row[58])->first();
            }

            if(!$row[58] || ($row[58] && $admin))
            {
                if(!$studentDiscipline) {
                    StudentDiscipline::insert([
                        'id' => $row[0],
                        'student_id' => $row[1],
                        'discipline_id' => $row[2],
                        'week1_result' => $row[3] ? $row[3] : null,
                        'week2_result' => $row[4] ? $row[4] : null,
                        'week3_result' => $row[5] ? $row[5] : null,
                        'week4_result' => $row[6] ? $row[6] : null,
                        'week5_result' => $row[7] ? $row[7] : null,
                        'week6_result' => $row[8] ? $row[8] : null,
                        'week7_result' => $row[9] ? $row[9] : null,
                        'test1_result' => $row[10] ? $row[10] : null,
                        'test1_result_points' => $row[11] ? $row[11] : null,
                        'test1_result_letter' => $row[12] ? $row[12] : null,
                        'test1_date' => $row[13] ? $row[13] : null,
                        'test1_result_trial' => $row[14] ? $row[14] : null,
                        'test1_blur' => $row[15] ? $row[15] : 0,
                        'test1_qr_checked' => $row[16] ? $row[16] : null,
                        'test1_max_points' => $row[17] ? $row[17] : null,
                        'week9_result' => $row[18] ? $row[18] : null,
                        'week10_result' => $row[19] ? $row[19] : null,
                        'week11_result' => $row[20] ? $row[20] : null,
                        'week12_result' => $row[21] ? $row[21] : null,
                        'week13_result' => $row[22] ? $row[22] : null,
                        'week14_result' => $row[23] ? $row[23] : null,
                        'week15_result' => $row[24] ? $row[24] : null,
                        'test_result' => $row[25] ? $row[25] : null,
                        'test_result_points' => $row[26] ? $row[26] : null,
                        'test_result_letter' => $row[27] ? $row[27] : null,
                        'test_date' => $row[28] ? $row[28] : null,
                        'test_manual' => $row[29] ? $row[29] : 0,
                        'test_result_trial' => $row[30] ? $row[30] : 0,
                        'test_blur' => $row[31] ? $row[31] : 0,
                        'test_qr_checked' => $row[32] ? $row[32] : 0,
                        'test_max_points' => $row[33] ? $row[33] : null,
                        'final_result' => $row[34] ? $row[34] : null,
                        'final_result_points' => $row[35] ? $row[35] : null,
                        'final_result_gpa' => $row[36] ? $row[36] : null,
                        'final_result_letter' => $row[37] ? $row[37] : null,
                        'final_date' => $row[38] ? $row[38] : null,
                        'final_manual' => $row[39] ? $row[39] : 0,
                        'analogue' => $row[40],
                        'notes' => $row[41],
                        'pay_processing' => $row[42] ? $row[42] : 0,
                        'payed' => $row[43] ? $row[43] : 0,
                        'payed_credits' => $row[44] ? $row[44] : null,
                        'free_credits' => $row[45] ? $row[45] : 0,
                        'remote_access' => $row[46] ? $row[46] : 0,
                        'iteration' => $row[47] ? $row[47] : 0,
                        'created_at' => $row[48] ? $row[48] : null,
                        'updated_at' => $row[49] ? $row[49] : null,
                        'syllabus_updated' => $row[50] ? $row[50] : 0,
                        'migrated' => $row[51] ? $row[51] : 0,
                        'recommended_semester' => $row[52] ? $row[52] : null,
                        'plan_semester' => $row[53] ? $row[53] : null,
                        'plan_semester_date' => $row[54] ? $row[54] : null,
                        'plan_semester_user_id' => $row[55] ? $row[55] : null,
                        'plan_admin_confirm' => $row[56] ? $row[56] : null,
                        'plan_admin_confirm_date' => $row[57] ? $row[57] : null,
                        'plan_admin_confirm_user_id' => $row[58] ? $row[58] : null,
                        'plan_student_confirm' => $row[59] ? $row[59] : null,
                        'plan_student_confirm_date' => $row[60] ? $row[60] : null,
                        'at_semester' => $row[61] ? $row[61] : 0,
                        'submodule_id' => $row[62] ? $row[62] : null,
                        'is_elective' => $row[63] ? $row[63] : 0,
                        'task_result' => $row[64] ? $row[64] : null,
                        'task_result_points' => $row[65] ? $row[65] : null,
                        'task_result_letter' => $row[66] ? $row[66] : null,
                        'task_date' => $row[67] ? $row[67] : null,
                        'task_manual' => $row[68] ? $row[68] : 0,
                        'task_blur' => $row[69] ? $row[69] : 0,
                        'archive' => 0
                    ]);
                }
                else
                {
                    $this->info('Exists ' . $row[0]);
                }
            } else
            {
                $this->info('admin not found');
            }



            $this->output->progressAdvance();
            //return;
        }

        $this->output->progressFinish();
    }
}
