<?php

namespace App\Console\Commands\Fix;

use App\Discipline;
use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class ClearDisciplinePay extends Command
{
    const FIELD_FIO = 0;
    const FIELD_DISCIPLINE_ID = [
        1 => 8,
        2 => 10,
        3 => 8,
        4 => 7
    ];
    const FIELD_DISCIPLINE_NAME = [
        1 => 3,
        2 => 3,
        3 => 4,
        4 => 3
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:discipline:pay {--checkonly=true} {--file=1}';

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
        $checkOnly = $this->option('checkonly') == 'true' ? true : false;
        $fileNum = $this->option('file');
        $file = fopen(storage_path('import/delete_discipline_pay_' . $fileNum . '.csv'), 'r');
        $updatedCount = 0;

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            $fio = $row[self::FIELD_FIO];
            $disciplineId = $row[self::FIELD_DISCIPLINE_ID[$fileNum]];
            $disciplineName = $row[self::FIELD_DISCIPLINE_NAME[$fileNum]];

            if(!$disciplineId)
            {
                continue;
            }

            $studentDiscipline = StudentDiscipline
                ::select(['students_disciplines.*'])
                ->leftJoin('profiles', 'profiles.user_id', '=', 'students_disciplines.student_id')
                ->where('profiles.fio', $fio)
                ->where('students_disciplines.discipline_id', $disciplineId)
                ->first();

            if($studentDiscipline)
            {
                if(!$checkOnly)
                {
                    $studentDiscipline->payed = 0;
                    $studentDiscipline->payed_credits = null;
                    $studentDiscipline->save();
                }

                $updatedCount++;
            }
            else
            {
                $this->warn('Relation not found ' . $fio . ' - ' . $disciplineName . '(' . $disciplineId . ')');
            }
        }

        $this->info('Updated count: ' . $updatedCount);
    }
}
