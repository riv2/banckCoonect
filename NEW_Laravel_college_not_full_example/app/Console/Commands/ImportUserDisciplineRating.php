<?php

namespace App\Console\Commands;

use App\Discipline;
use App\SpecialityDiscipline;
use App\StudentDiscipline;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportUserDisciplineRating extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:discipline:rating:import {--year=2019} {--check=false}';

    /**
     * The console command description.
     *
     * @var stringgit
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
        $year = $this->option('year');
        $check = $this->option('check') == 'true' ? true : false;

        if($check)
        {
            $this->warn('Check mode');
        }

        $file = fopen(storage_path('import/import_users_rating_' . $year . '.csv'), 'r');
        $fileRowCount = sizeof (file (storage_path('import/import_users_rating_' . $year . '.csv')));

        /*Create file cache*/
        $this->info('Create cache by csv');
        $this->output->progressStart( $fileRowCount );
        $csvCache = [];

        while($row = fgetcsv($file, 0, ',', "'"))
        {
            /*$userExId = $row[0];
            $disciplineExId = $row[21];
            $rating = $row[23];
            $csvCache[$userExId][$disciplineExId] = $rating;*/

            if($row[14] !== 0)
            {
                $userExId = $row[0];
                //$disciplineExId = $row[21];
                $rating = $row[13];

                if($year == 2018)
                {
                    if($row[14] && $this->hasStudentDiscipline($userExId, $row[14]))
                    {
                        $csvCache[$userExId][$row[14]] = $rating;
                    }
                    elseif($row[15] && $this->hasStudentDiscipline($userExId, $row[15]))
                    {
                        $csvCache[$userExId][$row[15]] = $rating;
                    }
                    elseif($row[16] && $this->hasStudentDiscipline($userExId, $row[16]))
                    {
                        $csvCache[$userExId][$row[16]] = $rating;
                    }
                    elseif($row[17] && $this->hasStudentDiscipline($userExId, $row[17]))
                    {
                        $csvCache[$userExId][$row[17]] = $rating;
                    }
                }
                else
                {
                    $disciplineId = $row[15];
                    $csvCache[$userExId][$disciplineId] = $rating;
                }

            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();

        $this->output->progressStart(count($csvCache));
        $updatedRows = 0;
        $specialIdList = $this->getSpecialIdList();

        $fileReport = fopen(storage_path('import/import_users_rating_' . $year . '_report.csv'), 'w');
        fputcsv($fileReport, [
            'User ID',
            'ФИО',
            'Дисциплина',
            'ID дисциплины(miras_app)',
            'Новый балл',
            'Оплачено кредитов',
            'T1',
            'СРО',
            'Экзамен',
            'Итоговая оценка',
            'Балл обновлен'
        ]);

        foreach($csvCache as $userExId => $exDisciplineList)
        {
            /*if(!in_array( $userExId, $specialIdList))
            {
                continue;
            }*/

            $user = User::with('studentProfile')->where('ex_id', $userExId)->first();

            if($user)
            {
                $studentDisciplines = StudentDiscipline
                    ::with('discipline')
                    ->with('user')
                    ->where('student_id', $user->id)
                    /*->whereNull('payed_credits')
                    ->whereNull('test1_result')
                    ->whereNull('test_result')
                    ->whereNull('final_result')
                    ->whereNull('task_result')*/
                    ->get();




                foreach($studentDisciplines as $studentDiscipline)
                {
                    if(isset($exDisciplineList[$studentDiscipline->discipline->id]))
                    {
                        $status = false;
                        $oldFinalResult = $studentDiscipline->final_result;

                        if(
                            $exDisciplineList[$studentDiscipline->discipline->id] != 0
                            && $this->needUpdateRating($studentDiscipline, $exDisciplineList[$studentDiscipline->discipline->id])
                        )
                        {
                            $status = true;

                            if($exDisciplineList[$studentDiscipline->discipline->id] && !$check)
                            {
                                $studentDiscipline->setFinalResult($exDisciplineList[$studentDiscipline->discipline->id]);
                                $studentDiscipline->payed = true;
                                $studentDiscipline->save();
                            }
                        }

                        $resultRow = [
                            $studentDiscipline->student_id,
                            $studentDiscipline->user->studentProfile->fio ?? '',
                            $studentDiscipline->discipline->name,
                            $studentDiscipline->discipline_id,
                            $exDisciplineList[$studentDiscipline->discipline->id],
                            $studentDiscipline->payed_credits,
                            $studentDiscipline->test1_result,
                            $studentDiscipline->task_result,
                            $studentDiscipline->test_result,
                            $oldFinalResult,
                            $status ? 'да' : 'нет'
                        ];

                        fputcsv($fileReport, $resultRow);

                        $updatedRows++;
                    }
                }
            }

            $this->output->progressAdvance();
        }
        fclose($fileReport);
        $this->output->progressFinish();

        $this->info('Updated rows: ' . $updatedRows);
    }

    public function needUpdateRating($studentDiscipline, $primeResult)
    {
        if(!$studentDiscipline->final_result)
        {
            return true;
        }

        if(!$studentDiscipline->payed_credits)
        {
            return true;
        }
        else
        {
            if(
                $studentDiscipline->test1_result
                && $studentDiscipline->test_result
                && $studentDiscipline->task_result
                && $studentDiscipline->final_result < $primeResult
            )
            {
                return true;
            }
        }

        return false;
        /*$result = !$studentDiscipline->payed_credits
            && !$studentDiscipline->test1_result
            && !$studentDiscipline->test_result
            && !$studentDiscipline->final_result
            && !$studentDiscipline->task_result;

        $result = $result ||
            ($studentDiscipline->payed_credits
                && !$studentDiscipline->test1_result
                && !$studentDiscipline->test_result
                && !$studentDiscipline->final_result
                && !$studentDiscipline->task_result);

        $result = $result ||
            ($studentDiscipline->payed_credits
                && $studentDiscipline->test1_result
                && $studentDiscipline->test_result
                && $studentDiscipline->final_result
                && $studentDiscipline->task_result
                && $studentDiscipline->final_result < $primeResult);*/
    }

    public function getSpecialIdList()
    {
        return [
            18740
        ];
    }

    /**
     * @param $exUserId
     * @param $disciplineId
     * @return bool
     */
    public function hasStudentDiscipline($exUserId, $disciplineId)
    {
        return (bool)StudentDiscipline
            ::leftJoin('users', 'users.id', 'students_disciplines.student_id')
            ->where('users.ex_id', $exUserId)
            ->where('students_disciplines.discipline_id', $disciplineId)
            ->count();
    }
}
