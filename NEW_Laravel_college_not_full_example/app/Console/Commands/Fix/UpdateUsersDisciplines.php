<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUsersDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:disciplines:update';

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
        $file = fopen(storage_path('import/fix_archive_submodules.csv'), 'r');
        $fileReport = fopen(storage_path('import/updated_fix_archive_submodules.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/fix_archive_submodules.csv')));
        $this->output->progressStart($fileRowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {
            $userId = $row[0];
            $disciplineId = $row[3];

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $userId)
                ->where('discipline_id', $disciplineId)
                ->first();

            if ($studentDiscipline) {
                    $studentDiscipline->plan_admin_confirm = 1;
                    $studentDiscipline->plan_admin_confirm_date = Carbon::now();
                    $studentDiscipline->plan_admin_confirm_user_id = 96;
                    $studentDiscipline->plan_student_confirm = 1;
                    $studentDiscipline->plan_student_confirm_date = Carbon::now();
                    $studentDiscipline->update();

                    $row[] = 'confirmed by admin';
                    $row[] = 'confirmed by admin ' . Carbon::now();
                    $row[] = 'confirmed by admin id - 96';
                    $row[] = 'student confirmed';
                    $row[] = 'student confirmed ' . Carbon::now();
            } else {
                $row[] = 'student_discipline not found';
            }

            $this->output->progressAdvance();
            fputcsv($fileReport, $row);
        }

        $this->output->progressFinish();
        fclose($fileReport);
    }
}
