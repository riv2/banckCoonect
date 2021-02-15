<?php

namespace App\Console\Commands\Export;

use App\Profiles;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class UserDisciplineTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:users:disciplines:test';

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
        $query = StudentDiscipline
            ::select([
                "students_disciplines.student_id as user_id",
                /*"profiles.fio as fio",
                "profiles.iin as iin",
                "profiles.mobile as mobile",
                "profiles.team as team",*/
                "disciplines.name as discipline_name",
                "disciplines.ects as disciplines_ects",
                "students_disciplines.test1_result as test1_result",
                "students_disciplines.is_elective as is_elective"
            ])
            ->leftJoin('disciplines', 'disciplines.id', '=', 'students_disciplines.discipline_id')
            ->leftJoin('users', 'users.id', '=', 'students_disciplines.student_id')
            ->whereNull('users.deleted_at');
            //->leftJoin('profiles', 'students_disciplines.student_id', '=', 'profiles.user_id');

        $count = $query;
        $count = $count->count();
        $this->output->progressStart($count);
        $userDiscCount = [];
        $userDiscPayCount = [];
        $profileInfo = [];
        $fileName = storage_path('export/export_users_discipline_test.csv');
        $file = fopen($fileName, 'w');

        fputcsv($file, [
            'ID',
            'ФИО',
            'ИИН',
            'Телефон',
            'Группа',
            'Количество дисциплин',
            'Название дисциплин',
            'Количество  кредитов',
            'Количество купленных дисциплин',
            'Результат теста',
            'Электив'
        ]);

        $query->chunk(1000, function($rows) use($userDiscCount, $userDiscPayCount, $profileInfo, $file){
            foreach ($rows as $row)
            {
                if(!isset($userDiscCount[$row->user_id]))
                {
                    $userDiscCount[$row->user_id] = StudentDiscipline::where('student_id', $row->user_id)->count();
                }

                if(!isset($userDiscPayCount[$row->user_id]))
                {
                    $userDiscPayCount[$row->user_id] = StudentDiscipline
                        ::where('student_id', $row->user_id)
                        ->where('payed_credits', '>', 0)
                        ->count();
                }

                if(!isset($profileInfo[$row->user_id]))
                {
                    $profileInfo[$row->user_id] = Profiles
                        ::where('user_id', $row->user_id)
                        ->first();
                }

                if(isset($profileInfo[$row->user_id]) && $profileInfo[$row->user_id]) {
                    fputcsv($file, [
                        $row->user_id,
                        $profileInfo[$row->user_id]->fio ?? '',
                        $profileInfo[$row->user_id]->iin ?? '',
                        $profileInfo[$row->user_id]->mobile ?? '',
                        $profileInfo[$row->user_id]->studyGroup->name ?? '',
                        $userDiscCount[$row->user_id] ?? '',
                        $row->discipline_name,
                        $row->disciplines_ects,
                        $userDiscPayCount[$row->user_id],
                        $row->test1_result === null ? '-' : $row->test1_result === null,
                        $row->is_elective ? 'да' : 'нет'
                    ]);
                }

                $this->output->progressAdvance();

            }
        });

        fclose($file);
        $this->output->progressFinish();
    }
}
