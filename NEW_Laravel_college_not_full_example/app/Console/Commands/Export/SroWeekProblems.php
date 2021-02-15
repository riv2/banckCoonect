<?php

namespace App\Console\Commands\Export;

use App\Discipline;
use App\StudentDiscipline;
use Illuminate\Console\Command;

class SroWeekProblems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sro:week:problems';

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
        $reportFile = fopen( storage_path('export/sro_week_problems.csv'), 'w' );
        $count = Discipline::count();
        $this->output->progressStart($count);

        fputcsv($reportFile, [
            'id дисциплины',
            'Дисциплина',
            'Сектор',
            'Количество студентов на 2019-20.2',
            'Язык',
            'id задания',
            'Проблема',
            'Админка'
        ]);

        Discipline
            ::with('syllabusTasks')
            ->with('syllabuses')
            ->with('sector')
            ->chunk(1000, function($disciplineList) use($reportFile){
                foreach ($disciplineList as $discipline)
                {
                    $studentDisciplineCount = StudentDiscipline
                        ::where('discipline_id', $discipline->id)
                        ->where('plan_semester', '2019-20.2')
                        ->count();

                    if($studentDisciplineCount) {
                        $reportTemplate = [
                            $discipline->id,
                            $discipline->name,
                            $discipline->sector->name ?? '',
                            $studentDisciplineCount
                        ];

                        if ($discipline->syllabusTasks) {
                            $weeks = [];

                            foreach ($discipline->syllabusTasks as $syllabusTask) {
                                $reportRow = $reportTemplate;
                                $reportRow[] = $syllabusTask->language;
                                $reportRow[] = $syllabusTask->id;
                                $fail = false;

                                if ($syllabusTask->week === null) {
                                    $reportRow[] = 'week is null';
                                    $fail = true;
                                } else {
                                    if (in_array(($syllabusTask->language . $syllabusTask->week), $weeks)) {
                                        $reportRow[] = 'week dublicate';
                                        $fail = true;
                                    } else {
                                        $weeks[] = $syllabusTask->language . $syllabusTask->week;
                                    }

                                }

                                if ($fail) {
                                    $syllabus = $discipline->syllabuses->where('language', $syllabusTask->language)->first();

                                    if ($syllabus) {
                                        $reportRow[] = route('adminSyllabusEdit', [
                                            'disciplineId' => $discipline->id,
                                            'themeId' => $syllabus->id
                                        ]);
                                    }


                                    fputcsv($reportFile, $reportRow);
                                }
                            }
                        }
                    }

                    $this->output->progressAdvance();
                }
            });

        $this->output->progressFinish();
        fclose($reportFile);
    }
}
