<?php

namespace App\Console\Commands\Import;

use App\Models\StudentDisciplineFile;
use App\StudentDiscipline;
use App\StudentPracticeFiles;
use Illuminate\Console\Command;

class ImportStudentFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:student:files {--part=1}';

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

        $fileRead = fopen(storage_path('import/import_student_files_' . $part . '.csv'), 'r');
        $fileReport = fopen(storage_path('import/import_student_files_' . $part . '_report.csv'), 'w');
        $fileRowCount = sizeof (file (storage_path('import/import_student_files_' . $part . '.csv')));
        $this->output->progressStart($fileRowCount);

        fputcsv($fileReport, [
            'ID студента',
            'ФИО',
            'ID специальности',
            'Дисциплина',
            'ID дисциплины',
            'Файл',
            'Новое имя',
            'Статус'
        ]);

        $newFileNameList = [];
        $uploadedFiles = [];

        while($row = fgetcsv($fileRead, 0, ',', '"')) {
            $newFileNameList[] = $row[0];
        }

        fclose($fileRead);
        $fileRead = fopen(storage_path('import/import_student_files_' . $part . '.csv'), 'r');

        while($row = fgetcsv($fileRead, 0, ',', '"')) {
            $fileName = $row[0];


            if(file_exists(storage_path('import_student_files/list' . $part . '/' . $fileName)))
            {
                $disciplineIdList = $this->getDisciplineIdList($row, $part);
                $specialityIdList = $this->getSpecialityIdList($row, $part);

                $studentDisciplineList = StudentDiscipline
                    ::with('discipline')
                    ->leftJoin('profiles', 'profiles.user_id', '=', 'students_disciplines.student_id')
                    /*->with('studentProfile')
                    ->whereHas('studentProfile', function($q) use ($specialityIdList) {
                        $q->whereIn('education_speciality_id', $specialityIdList);
                        //$q->whereDoesntHave('studentDisciplineFiles');
                    })*/
                    ->whereIn('profiles.education_speciality_id', $specialityIdList)
                    ->whereIn('discipline_id', $disciplineIdList)
                    ->get();

                foreach ($studentDisciplineList as $studentDiscipline)
                {
                    $studentDisciplineFilesCount = StudentDisciplineFile
                        ::where('user_id', $studentDiscipline->student_id)
                        ->where('discipline_id', $studentDiscipline->discipline_id)
                        ->whereNotIn('original_name', $newFileNameList)
                        ->count();

                    if($studentDisciplineFilesCount == 0) {

                        if(!StudentDisciplineFile
                            ::where('user_id', $studentDiscipline->student_id)
                            ->where('discipline_id', $studentDiscipline->discipline_id)
                            ->where('original_name', $fileName)
                            ->count())
                        {

                            $report = [
                                $studentDiscipline->student_id,
                                $studentDiscipline->fio,
                                $studentDiscipline->education_speciality_id,
                                $studentDiscipline->discipline->name,
                                $studentDiscipline->discipline_id,
                                $fileName
                            ];

                            if ($studentDiscipline->discipline->is_practice) {
                                $file = new StudentPracticeFiles();
                                $file->created_at = DB::raw('NOW()');
                            } else {
                                $file = new StudentDisciplineFile();
                            }

                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $extension = $extension ? '.' . $extension : '';

                            $newFileName = $uploadedFiles[$fileName] ?? ('syllabus_' . rand(1, 10000) . '_' . time() . rand(1, 1000) . $extension);

                            $file->type = 'file';
                            $file->user_id = $studentDiscipline->student_id;
                            $file->discipline_id = $studentDiscipline->discipline_id;
                            $file->file_name = $newFileName;
                            $file->original_name = $fileName;

                            if(!isset($uploadedFiles[$fileName]))
                            {
                                $copyResult = \File::copy(storage_path('import_student_files/list' . $part . '/' . $fileName), public_path('syllabus_documents/student_files/' . $file->file_name));
                                $uploadedFiles[$fileName] = $file->file_name;
                            }

                            $report[] = $file->file_name;

                            if ($copyResult) {
                                $file->save();
                                $report[] = 'success copy';
                            } else {
                                $report[] = 'fail copy';
                            }

                            fputcsv($fileReport, $report);
                        }
                    }
                    //$file['document']->move(public_path('syllabus_documents/student_files'), $filename);
                }

                $this->output->progressAdvance();
            }
            else
            {
                $this->warn($fileName);
            }
        }

        fclose($fileReport);
        $this->output->progressFinish();

    }

    /**
     * @param $row
     * @param $part
     * @return array
     */
    public function getDisciplineIdList($row, $part)
    {
        $disciplineIdList = [];

        if($part == 1)
        {
            for($i=1; $i<=2; $i++)
            {
                if( !empty($row[$i]) )
                {
                    $disciplineIdList[] = $row[$i];
                }
            }
        }

        if($part == 2)
        {
            if( !empty($row[1]) )
            {
                $disciplineIdList[] = $row[1];
            }
        }

        return $disciplineIdList;
    }

    /**
     * @param $row
     * @param $part
     * @return array
     */
    public function getSpecialityIdList($row, $part)
    {
        $specialityIdList = [];

        if($part == 1)
        {
            for($i=3; $i<=7; $i++)
            {
                if( !empty($row[$i]) )
                {
                    $specialityIdList[] = $row[$i];
                }
            }
        }

        if($part == 2)
        {
            for($i=2; $i<=21; $i++)
            {
                if( !empty($row[$i]) )
                {
                    $specialityIdList[] = $row[$i];
                }
            }
        }

        return $specialityIdList;
    }

}
