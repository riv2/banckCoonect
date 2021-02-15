<?php

namespace App\Console\Commands\Fix;

use App\Syllabus;
use App\SyllabusDocument;
use Illuminate\Console\Command;

class CheckSyllabusFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:syllabus:files';

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
        $query = SyllabusDocument::where('resource_type', SyllabusDocument::RESOURCE_TYPE_FILE);
        $rowCount = $query;
        $rowCount = $rowCount->count();
        $fileName = storage_path('export/syllabus_docs.csv');
        $file = fopen($fileName, 'w');

        $this->output->progressStart($rowCount);

        fputcsv($file, [
            'ID',
            'URL Темы',
            'Дисциплина',
            'Тема',
            'Документ'
        ]);

        $query->chunk(2000, function($docs) use($file){
            foreach ($docs as $doc)
            {
                $syllabus = Syllabus::with('discipline')->where('id', $doc->syllabus_id)->first();
                if(!$syllabus)
                {
                    $this->warn('Syllabus not found. doc_id = ' . $doc->id);
                }
                else
                {
                    if(!file_exists($doc->getFileBasePath()))
                    {
                        fputcsv($file, [
                            $doc->id,
                            route('adminSyllabusEdit', [
                                'disciplineId' => $syllabus->discipline_id,
                                'themeId' => $doc->syllabus_id
                            ]),
                            $syllabus->discipline->name ?? '',
                            $syllabus->theme_name ?? '',
                            $doc->filename_original,
                        ]);
                    }
                }

                $this->output->progressAdvance();
            }
        });

        fclose($file);
        $this->output->progressFinish();
    }
}
