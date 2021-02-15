<?php

namespace App\Console\Commands\Import;

use App\AdminUserDiscipline;
use App\Discipline;
use App\StudyGroup;
use App\StudyGroupTeacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class StudyGroupTeacherImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:study_group_teacher {--part=1}';

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
        $part = $this->option('part');

        $file = fopen(storage_path('import/import_study_group_teacher_' . $part . '.csv'), 'r');
        $rowCount = sizeof (file (storage_path('import/import_study_group_teacher_' . $part . '.csv')));
        $fileReport = fopen(storage_path('import/import_study_group_teacher_' . $part . '_report.csv'), 'w');

        $this->output->progressStart($rowCount);
        $teacherId = 18621;
        $disciplineMaxCount = ($part == 1 || $part == 3) ? 1 : 8;

        while($row = fgetcsv($file, 0, ',', '"'))
        {
            $groupId = $row[6];
            $disciplineIdIndex = $part == 1 ? 10 : 7;
            $group = StudyGroup::where('id', $groupId)->first();
            $maxIndex = $disciplineIdIndex + $disciplineMaxCount;

            if($group)
            {
                for($i=$disciplineIdIndex; $i < $maxIndex; $i++) {

                    $disciplineId = $row[$disciplineIdIndex];

                    if($disciplineId) {

                        $discipline = Discipline::where('id', $disciplineId)->first();

                        if ($discipline) {
                            $adminUserDiscipline = AdminUserDiscipline
                                ::where('user_id', $teacherId)
                                ->where('discipline_id', $discipline->id)
                                ->first();

                            if (!$adminUserDiscipline) {
                                AdminUserDiscipline::insert([
                                    'user_id' => $teacherId,
                                    'discipline_id' => $discipline->id,
                                    'created_at' => DB::raw('NOW()'),
                                    'updated_at' => DB::raw('NOW()'),
                                ]);
                                $row[] = 'success (' . $discipline->id . ')';
                            }

                            $studyGroupTeacher = StudyGroupTeacher
                                ::where('user_id', $teacherId)
                                ->where('study_group_id', $group->id)
                                ->where('discipline_id', $discipline->id)
                                ->first();

                            if (!$studyGroupTeacher) {
                                StudyGroupTeacher::insert([
                                    'user_id' => $teacherId,
                                    'discipline_id' => $discipline->id,
                                    'study_group_id' => $group->id,
                                    'created_at' => DB::raw('NOW()'),
                                    'updated_at' => DB::raw('NOW()'),
                                ]);
                                $row[] = 'success (' . $discipline->id . ')';
                            }

                        } else {
                            $row[] = 'discipline not found (' . $disciplineId . ')';
                        }
                    }

                    $disciplineIdIndex++;
                }
            }
            else
            {
                $row[] = 'group not found';
            }

            fputcsv($fileReport, $row);
            $this->output->progressAdvance();
        }

        fclose($fileReport);
        fclose($file);

        $this->output->progressFinish();
    }
}
