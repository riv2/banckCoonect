<?php

namespace App\Console\Commands\Fix;

use App\Http\Controllers\Student\PromotionController;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixSubmoduleSemester2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:submodule:semester2';

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
        $file = fopen(storage_path('import/fix_submodule_semester_2.csv'), 'r');
        $reportFile = fopen(storage_path('import/fix_submodule_semester_2_report.csv'), 'w');
        $rowCount = sizeof (file (storage_path('import/fix_submodule_semester_2.csv')));

        $this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '\''))
        {
            if($row[9] == 'not empty' && $row[10] == '2')
            {
                $userId = $row[0];
                $disciplineId = $row[6];

                $studentDiscipline = StudentDiscipline
                        ::where('student_id', $userId)
                        ->where('discipline_id', $disciplineId)
                        ->first();

                if($studentDiscipline)
                {
                    $studentDiscipline->recommended_semester = 1;
                    $studentDiscipline->save();

                    $nextLevel = $this->getNextLevel($studentDiscipline->submodule_id, $studentDiscipline->discipline_id);

                    if($nextLevel)
                    {
                        $newDiscipline = $this->addDisciplineToStudent(
                            $userId,
                            $nextLevel,
                            $studentDiscipline->submodule_id,
                            2
                        );

                        if($newDiscipline)
                        {
                            $row[] = 'add next disc ' . $newDiscipline;
                        }
                        else
                        {
                            $row[] = 'fail add next disc';
                        }
                    }
                    else
                    {
                        $row[] = 'next level not found';
                    }
                }
                else
                {
                    $row[] = 'student_discipline not found';
                }

                fputcsv($reportFile, $row);
            }
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
    }

    /**
     * @param $submoduleId
     * @param $currentDisciplineId
     * @return mixed|null
     */
    public function getNextLevel($submoduleId, $currentDisciplineId)
    {

        $submodules = [
            1 => [
                2549,
                2550,
                897,
                898,
            ],
            2 => [
                2550,
                897,
                898,
                899
            ],
            3 => [
                901,
                2721,
                902,
                2722,
                903,
                2723,
                904,
                2724,
                905
            ],
            4 => [
                902,
                2722,
                903,
                2723,
                904,
                2724,
                905,
                906
            ]
        ];

        foreach ($submodules[$submoduleId] as $k => $disciplineId)
        {
            if($disciplineId == $currentDisciplineId)
            {
                return $submodules[$submoduleId][$k + 1] ?? null;
            }
        }

        return null;
    }

    /**
     * @param $student_id
     * @param $disciplineId
     * @param $submoduleId
     * @param $semester
     * @return int
     */
    public function addDisciplineToStudent($student_id, $disciplineId, $submoduleId, $semester)
    {
        $studentDiscipline = new StudentDiscipline();

        $studentDiscipline->student_id = $student_id;
        $studentDiscipline->discipline_id = $disciplineId;
        $studentDiscipline->submodule_id = $submoduleId;
        $studentDiscipline->recommended_semester = $semester;

        $studentDiscipline->save();

        return $studentDiscipline->id;
    }
}
