<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShiftOfDisciplines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'disciplines:shift';

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
        $file = fopen(storage_path('import/shift_of_disciplines.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/shift_of_disciplines.csv')));

        //$this->output->progressStart($rowCount);

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $this->info('-----');
            $specialityId = $row[0];
            $disciplineNewId = $row[1];
            $disciplineOldId = $row[2];
            $disciplineExId = $row[3];
            $semester = $row[4];

            $this->info('speciality_id = ' . $specialityId);
            $this->info('disicpline_new_id = ' . $disciplineNewId);
            $this->info('disicpline_input_old_id = ' . $disciplineOldId);
            $this->info('disicpline_ex_id = ' . $disciplineExId);
            $this->info('semester = ' . $semester);

            $this->shiftDiscipline($specialityId, $disciplineNewId, $disciplineExId, $semester, $disciplineOldId);
            $this->info('-----');
        }
    }

    /**
     * @param $specialityId
     * @param $disciplineNewId
     * @param $disciplineExId
     * @param $semester
     * @param null $disciplineOldId
     */
    public function shiftDiscipline(
        $specialityId,
        $disciplineNewId,
        $disciplineExId,
        $semester,
        $disciplineOldId = null
    )
    {
        $oldDisciplineIdList = $disciplineOldId ? [$disciplineOldId] : [];

        if(count($oldDisciplineIdList) == 0)
        {
            $disciplineList = Discipline
                ::select(['disciplines.id as id'])
                ->leftJoin('speciality_discipline', 'speciality_discipline.discipline_id', '=', 'disciplines.id')
                ->where('disciplines.ex_id', $disciplineExId)
                ->where('disciplines.id', '!=', $disciplineNewId)
                //->where('speciality_discipline.semester', $semester)
                ->where('speciality_discipline.speciality_id', $specialityId)
                ->get();

            $oldDisciplineIdList = $disciplineList ? $disciplineList->pluck('id')->toArray() : [];
        }

        $this->info( 'Old disciplines: ' . implode(',', $oldDisciplineIdList));

        if(count($oldDisciplineIdList) > 0)
        {
            $this->updateDisciplineRelations($specialityId, $oldDisciplineIdList, $disciplineNewId, $semester);
            //$this->updateSpecialityRelations($specialityId, $disciplineNewId, $oldDisciplineIdList[0], $semester);
        }
        else
        {
            $this->warn('Old discipline not found: Speciality = ' . $specialityId . '. Discipline ex id = ' . $disciplineExId);
        }
    }

    /**
     * @param $specialityId
     * @param $oldDisciplineIdList
     * @param $disciplineNewId
     * @return int
     */
    public function updateDisciplineRelations($specialityId, $oldDisciplineIdList, $disciplineNewId, $semester)
    {
        $studentDisciplineList = StudentDiscipline
            ::select(['students_disciplines.id as id'])
            ->leftJoin('profiles', 'profiles.user_id', '=', 'students_disciplines.student_id')
            ->whereIn('discipline_id', $oldDisciplineIdList)
            ->where('profiles.education_speciality_id', $specialityId);

        $hasPayed = clone $studentDisciplineList;
        $hasPayed = (bool)$hasPayed->whereNotNull('students_disciplines.payed_credits')->count();

        if($hasPayed)
        {
            $this->warn('Has payed: speciality = ' . $specialityId . '; disicpline = ' . $oldDisciplineIdList[0]);
            return 0;
        }

        $studentDisciplineList = $studentDisciplineList->get();
        foreach($studentDisciplineList as $studentDiscipline)
        {
            $studentDiscipline->semester = $semester ? $semester : $studentDiscipline->semester;
            $studentDiscipline->discipline_id = $disciplineNewId;
            $studentDiscipline->save();
        }


        $idListForUpdate = $studentDisciplineList ? $studentDisciplineList->pluck('id')->toArray() : [];

        //StudentDiscipline::whereIn('id', $idListForUpdate)->update(['discipline_id' => $disciplineNewId]);

        $this->info('Updated students_disciplines: ' . implode(',', $idListForUpdate));

        return count($idListForUpdate);
    }

    /**
     * @param $specialityId
     * @param $disciplineNewId
     * @param $disciplineOldId
     * @return mixed
     */
    public function updateSpecialityRelations($specialityId, $disciplineNewId, $disciplineOldId, $semester = null)
    {
        $query = SpecialityDiscipline
            ::where('speciality_id', $specialityId)
            ->where('discipline_id', $disciplineOldId);

        $specialityDiscipline = $query;
        $specialityDiscipline = $specialityDiscipline->first();

        if(!$specialityDiscipline)
        {
            $specialityDiscipline = SpecialityDiscipline
                ::where('speciality_id', $specialityId)
                ->where('discipline_id', $disciplineNewId)
                ->first();

            if(!$specialityDiscipline)
            {
                $this->warn('speciality_discipline not found: speciality_id: ' . $specialityId . '; discipline_id: ' . $disciplineNewId);
                return;
            }
            else
            {
                if($specialityDiscipline->semester != $semester)
                {
                    $this->warn('Semester colision. speciality_id = ' . $specialityId . '; discipline_id = ' . $disciplineNewId . '; current_semester = ' . $specialityDiscipline->semester . '; semester_in_file = ' . $semester);
                }
            }
        }
        else
        {
            if($specialityDiscipline->semester != $semester)
            {
                $this->warn('Semester colision. speciality_id = ' . $specialityId . '; discipline_id = ' . $disciplineOldId . '; current_semester = ' . $specialityDiscipline->semester . '; semester_in_file = ' . $semester);
            }

            $specialityDiscipline->discipline_id = $disciplineNewId;
            $specialityDiscipline->semester = $semester ? $semester : $specialityDiscipline->semester;
            $specialityDiscipline->save();
        }

    }
}