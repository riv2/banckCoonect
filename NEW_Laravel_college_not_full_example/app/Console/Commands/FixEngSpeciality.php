<?php

namespace App\Console\Commands;

use App\Discipline;
use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class FixEngSpeciality extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:eng:speciality';

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
        $this->updateBySpeciality(74, 79);
        $this->updateBySpeciality(7, 83);
    }

    /**
     * @param $specialityId
     * @param $newSpecialityId
     */
    public function updateBySpeciality($specialityId, $newSpecialityId)
    {
        $profileList = Profiles
            ::whereHas('user', function($query){
                $query->where('keycloak', true);
                $query->where('import_type', 'eng_test');
            })
            ->where('education_speciality_id', $specialityId)
            ->get();

        $this->info(count($profileList));

        foreach ($profileList as $profile)
        {
            StudentDiscipline
                ::where('student_id', $profile->user_id)
                ->delete();

            $newDisciplineList = [895, 896, 897];

            foreach ($newDisciplineList as $newDiscipline)
            {
                $studentDiscipline = new StudentDiscipline();
                $studentDiscipline->discipline_id = $newDiscipline;
                $studentDiscipline->student_id = $profile->user_id;
                $studentDiscipline->payed = true;
                $studentDiscipline->iteration = 0;
                $studentDiscipline->save();
            }
        }

        Profiles
            ::whereHas('user', function($query){
                $query->where('keycloak', true);
                $query->where('import_type', 'eng_test');
            })
            ->where('education_speciality_id', $specialityId)
            ->update([
                'education_speciality_id' => $newSpecialityId
            ]);
    }
}
