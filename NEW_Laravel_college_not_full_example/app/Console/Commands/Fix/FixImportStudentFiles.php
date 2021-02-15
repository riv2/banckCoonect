<?php

namespace App\Console\Commands\Fix;

use App\Models\StudentDisciplineFile;
use Illuminate\Console\Command;

class FixImportStudentFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:import:student:files {--part=1}';

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
        $part = (int)$this->option('part');

        $fileRead = fopen(storage_path('import/import_student_files_' . $part . '_report.csv'), 'r');
        //$fileReport = fopen(storage_path('import/import_student_files_' . $part . '_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/import_student_files_' . $part . '_report.csv')));
        $this->output->progressStart($fileRowCount);

        $updatedList = [];
        $forDelete = [];

        while($row = fgetcsv($fileRead, 0, ',', '"'))
        {
            $userId = $row[0];
            $disciplineId = $row[4];

            /*if($userId != 10528)
            {
                $this->output->progressAdvance();
                continue;
            }*/

            $key = $userId . '-' . $disciplineId;

            if( !in_array($key, $updatedList) )
            {
                $studentDisciplineFiles = StudentDisciplineFile
                    ::where('type', 'file')
                    ->where('user_id', $userId)
                    ->where('discipline_id', $disciplineId)
                    ->get();

                foreach ($studentDisciplineFiles as $k => $studentDisciplineFile)
                {
                    if($k > 0)
                    {
                        $forDelete[] = $studentDisciplineFile->id;
                    }
                }

                $updatedList[] = $key;
            }

            $this->output->progressAdvance();
        }

        StudentDisciplineFile::whereIn('id', $forDelete)->forceDelete();

        $this->output->progressFinish();
    }
}
