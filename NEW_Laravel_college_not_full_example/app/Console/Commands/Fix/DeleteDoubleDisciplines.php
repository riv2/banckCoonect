<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\DisciplineModule;
use App\DisciplineSubmodule;
use App\QuizResult;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class DeleteDoubleDisciplines extends Command
{
    const FIELD_DOUBLE_ID = 0;
    const FIELD_DOUBLE_NAME = 1;
    const FIELD_ects = 2;
    const FIELD_RIGHT_ID = 4;
    const FIELD_RIGHT_NAME = 5;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:disciplines:double';

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
        $file = fopen(storage_path('import/delete_double_disciplines.csv'), 'r');

        $resultCount = 0;
        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $doubleId = $row[self::FIELD_DOUBLE_ID];
            $rightId = $row[self::FIELD_RIGHT_ID];



            if($doubleId)
            {
                $studentRelationCount = StudentDiscipline::where('discipline_id', $doubleId)->count();
                $QuizResultCount = QuizResult::where('discipline_id', $doubleId)->count();
                $specialityDisciplineCount = SpecialityDiscipline::where('discipline_id', $doubleId)->count();
                $submoduleRelationCount = DisciplineSubmodule::where('discipline_id', $doubleId)->count();
                $moduleRelationCount = DisciplineModule::where('discipline_id', $doubleId)->count();

                if($rightId)
                {
                    $rightDiscipline = Discipline::where('id', $rightId)->first();

                    if($rightDiscipline)
                    {
                        StudentDiscipline::where('discipline_id', $doubleId)->update([
                            'discipline_id' => $rightDiscipline->id
                        ]);

                        QuizResult::where('discipline_id', $doubleId)->update([
                            'discipline_id' => $rightDiscipline->id
                        ]);

                        SpecialityDiscipline::where('discipline_id', $doubleId)->update([
                            'discipline_id' => $rightDiscipline->id
                        ]);

                        DisciplineSubmodule::where('discipline_id', $doubleId)->update([
                            'discipline_id' => $rightDiscipline->id
                        ]);

                        DisciplineModule::where('discipline_id', $doubleId)->update([
                            'discipline_id' => $rightDiscipline->id
                        ]);


                        $this->info(
                            'Updated ' . $doubleId . ' to ' . $rightDiscipline->id .
                            '. Student relation count = ' . $studentRelationCount .
                            '. Quize result count = ' . $QuizResultCount .
                            '. Speciality relation count = ' . $specialityDisciplineCount .
                            '. Submodule relation count = ' . $submoduleRelationCount .
                            '. Module relation count = ' . $moduleRelationCount);

                        Discipline::where('id', $doubleId)->delete();
                        $this->info('Deleted ' . $doubleId);
                    }
                    else
                    {
                        $this->warn('Right discipline ' . $rightId . ' not found');
                    }
                }
                else
                {
                    if($studentRelationCount)
                    {
                        $this->warn('Discipline ' . $doubleId . ' has students_disciplines relations');
                    }
                    elseif($QuizResultCount)
                    {
                        $this->warn('Discipline ' . $doubleId . ' has quize_result relations');
                    }
                    elseif($specialityDisciplineCount)
                    {
                        $this->warn('Discipline ' . $doubleId . ' has speciality relations');
                    }
                    elseif($submoduleRelationCount)
                    {
                        $this->warn('Discipline ' . $doubleId . ' has submodule relations');
                    }
                    elseif($moduleRelationCount)
                    {
                        $this->warn('Discipline ' . $doubleId . ' has module relations');
                    }
                    else
                    {
                        Discipline::where('id', $doubleId)->delete();
                        $this->info('Deleted ' . $doubleId);
                    }
                }

                $resultCount++;
            }
        }
        $this->info('Result count = ' . $resultCount);
    }
}
