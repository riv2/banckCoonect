<?php

namespace App\Console\Commands\Test;

use App\QuizQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckQuestionAudio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:audio:check';

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
        Log::useDailyFiles(storage_path('logs/test_audio_' . date('Y_m_d', time()) . '.log'));

        $questionList = QuizQuestion
            ::with('audiofiles')
            ->with('syllabuses')
            ->whereHas('audiofiles', function($query){
                $query->whereRaw("filename like '%.docx'");
            })
            ->whereNotNull('quize_questions.id')
            ->get();

        foreach ($questionList as $question)
        {
            foreach ($question->audiofiles as $file)
            {
                if( !file_exists(public_path('audio/' . $file->filename)) )
                {
                    Log::info('Audio file not found', [
                        'question.id' => $question->id,
                        'discipline'  => $question->syllabus->discipline_id ?? 'null',
                        'syllabus.id' => $question->syllabus->id ?? 'null',
                        'file.id' => $file->id,
                        'file.name' => $file->filename
                    ]);
                }
            }
        }
    }
}
