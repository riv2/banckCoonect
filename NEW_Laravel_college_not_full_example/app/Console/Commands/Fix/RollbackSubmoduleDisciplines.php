<?php

namespace App\Console\Commands\Fix;;

use App\StudentDiscipline;
use App\StudentSubmodule;
use Illuminate\Console\Command;

class RollbackSubmoduleDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rollback:submodule:disciplines';

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
        // SELECT * FROM `students_disciplines` WHERE `payed` = 0 AND payed_credits IS NULL &&`submodule_id` IN (1,3)
        // SELECT distinct students_disciplines.student_id, profiles.fio, profiles.mobile FROM `students_disciplines` join profiles ON profiles.user_id = students_disciplines.student_id WHERE `payed` = 0 AND payed_credits IS NULL && `submodule_id` IN (1, 3)


        $studentDisciplines = StudentDiscipline::where('payed', 0)
            ->whereNull('payed_credits')
            ->whereIn('submodule_id', [1, 3])
            ->get();

        foreach ($studentDisciplines as $studentDiscipline) {
            /** @var StudentDiscipline $studentDiscipline */

            $studentId = $studentDiscipline->student_id;
            $submodule1Id = $studentDiscipline->submodule_id;
            $submodule2Id = ($studentDiscipline->submodule_id == 1) ? 2 : 4;

            $this->addStudentSubmodule($studentId, $submodule1Id);

            $studentDiscipline->delete();

            // Second link
            $studentDiscipline2 = StudentDiscipline::where('student_id', $studentId)
                ->where('submodule_id', $submodule2Id)
                ->where('payed', 0)
                ->whereNull('payed_credits')
                ->first();

            if (!empty($studentDiscipline2)) {
                $this->addStudentSubmodule($studentId, $submodule2Id);

                $studentDiscipline2->delete();
            }
        }
    }

    private function addStudentSubmodule(int $studentId, int $submoduleId)
    {
        $exists = StudentSubmodule::where('student_id', $studentId)
            ->where('submodule_id', $submoduleId)
            ->exists();

        // Add submodule
        if (!$exists) {
            $studentSubmodule = new StudentSubmodule();
            $studentSubmodule->submodule_id = $submoduleId;
            $studentSubmodule->student_id = $studentId;
            $studentSubmodule->save();
        }
    }
}
