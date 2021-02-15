<?php

namespace App\Console\Commands;

use DateTime;
use Illuminate\Console\Command;
use App\LibraryKnowledgeSection;
use App\LibraryLiteratureCatalog;
use Maatwebsite\Excel\Facades\Excel;
use App\LibratyCatalogKnowledgeSection;

class LibraryTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library_transfer';

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
        $transfer = Excel::load(public_path('library_dump2.xlsx'));
        $transfer = $transfer->toArray();

        foreach ($transfer as $value) {
            $record = [
                'name' => $value['name']?? '',
                'media' => $value['media'],
                'literature_type' => 'Другое',
                'publication_type' => $value['publication_type']?? 'другое',
                'publication_year' => DateTime::createFromFormat('Y', intval($value['publication_year'])) !== FALSE? intval($value['publication_year']) : '2020',
                'isbn' => $value['isbn'],
                'ydk' => $value['ydk'],
                'bbk' => $value['bbk'],
                'author' => $value['author']?? '',
                'more_authors' => $value['more_authors'],
                'language' => $value['language']?? '',
                'number_pages' => $value['number_pages']?? '',
                'key_words' => $value['key_words'],
                'cost' => '0',
                'receipt_date' => '2020-12-12',
                'source_income' => ''
            ];

            $catalogRecord = LibraryLiteratureCatalog::create($record);
            $knowledgeID = LibraryKnowledgeSection::where('name', $value['knowledge_section'])->value('id');

            if($knowledgeID){
                LibratyCatalogKnowledgeSection::create([
                    'literature_catalog_id' => $catalogRecord->id,
                    'knowledge_section_id'  => $knowledgeID
                ]);
            }
        }

        dd('ok');
    }
}
