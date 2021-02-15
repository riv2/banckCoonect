<?php

namespace App\Console\Commands;

use App\Profiles;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ElectiveMove extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elective:move';

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
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                /** @var User $user */

                if ($user->study_year == 1 || $user->studentProfile->category == Profiles::CATEGORY_TRANSIT || $user->studentProfile->category == Profiles::CATEGORY_TRANSFER) {
                    continue;
                }

                $electives = StudentDiscipline::where('student_id', $user->id)
                    ->where('is_elective', 1)
                    ->pluck('discipline_id')
                    ->toArray();

                $specialityDisciplines = SpecialityDiscipline::where('speciality_id', $user->studentProfile->education_speciality_id)
                    ->pluck('discipline_id')
                    ->toArray();

                $forMove = array_intersect($electives, $specialityDisciplines);

                foreach ($forMove as $disciplineId) {
                    $studentDiscipline = StudentDiscipline::where('student_id', $user->id)
                        ->where('discipline_id', $disciplineId)
                        ->where('is_elective', 1)
                        ->first();

                    $studentDiscipline->is_elective = 0;

                    $studentDiscipline->save();
                }
            }
        });
    }
}
