<?php

namespace App\Console\Commands\Fix;

use App\StudentDiscipline;
use Illuminate\Console\Command;

class ClearMigratedStudentDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:migrated:student_disciplines';

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
        $file = fopen(storage_path('import/pay_clear_by_list.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/pay_clear_by_list.csv')));
        $updatedCount = 0;

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"')) {
            $studentId = $row[0];
            $disciplineId = $row[1];

            if (!$disciplineId || !$studentId) {
                continue;
            }

            $studentDiscipline = StudentDiscipline
                ::where('student_id', $studentId)
                ->where('discipline_id', $disciplineId)
                ->whereNull('final_result')
                ->where('migrated', 1)
                ->whereNull('payed_credits')
                ->where('payed', 0)
                ->first();

            if ($studentDiscipline) {
                $studentDiscipline->migrated = false;
                $studentDiscipline->save();
                $updatedCount++;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->info('Updated: ' . $updatedCount);
    }
}
