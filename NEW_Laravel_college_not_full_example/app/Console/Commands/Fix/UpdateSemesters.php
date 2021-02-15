<?php

namespace App\Console\Commands\Fix;

use App\SpecialityDiscipline;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class UpdateSemesters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'semesters:update';

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
        $updated = 0;
        $specialityDisciplineNotFound = 0;

        $this->output->progressStart(StudentDiscipline::where('is_elective', 1)->count());

        StudentDiscipline
            ::where('is_elective', 1)
            ->chunk(2000, function($studentDisciplines) use(&$updated, &$specialityDisciplineNotFound){
            foreach ($studentDisciplines as $studentDiscipline)
            {
                //$specialityId = $studentDiscipline->user->studentProfile->education_speciality_id ?? null;
                $electiveSpecialityId = $studentDiscipline->user->studentProfile->elective_speciality_id ?? null;

                if($electiveSpecialityId)
                {
                    $specialityDiscipline = SpecialityDiscipline
                        ::where('speciality_id', $electiveSpecialityId)
                        ->where('discipline_id', $studentDiscipline->discipline_id)
                        ->first();

                    if ($specialityDiscipline) {
                        if ($specialityDiscipline->semester != $studentDiscipline->semester) {
                            $studentDiscipline->semester = $specialityDiscipline->semester;
                            $studentDiscipline->save();
                            $updated++;
                        }
                    } else {
                        $specialityDisciplineNotFound++;
                    }
                }

                $this->output->progressAdvance();
            }
        });

        $this->output->progressFinish();

        $this->info('Updated: ' . $updated);
        $this->info('Speciality Discipline Not Found: ' . $specialityDisciplineNotFound);
    }
}
