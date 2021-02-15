<?php
namespace App\Console\Commands\Import;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportQuiz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:quiz {delimiter=,}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import quiz --options=delimiter=,';

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
        $quizName = $this->ask('Select file to import: 2168, 2169, or name of file without extension. Example:2168');
 
        $quizFile = storage_path('import/' . $quizName . '.csv');
        $delimiter = $this->argument('delimiter');
        
        if (!file_exists($quizFile)) {
            $this->output->error($quizName . '.csv in storage path: storage/import - not exist.');
            die();
        }         
        
        $file = fopen($quizFile, 'r');      
        $fileRowCount = sizeof (file ($quizFile));
        $this->output->progressStart($fileRowCount);
        
        $quizArr      = [];      
        $quizAnswerArr = [];

        while($oneQuizRowCsv = fgetcsv($file, 0, $delimiter, '"'))
        {  
           $question = $oneQuizRowCsv[2];           
           $quizArr[$question]['discipline_id']  = $oneQuizRowCsv[0];
           $quizArr[$question]['lang']           = $oneQuizRowCsv[1];
           $quizAnswerArr[$question]['answer'][] = ['answer' => $oneQuizRowCsv[3], 'correct' => $oneQuizRowCsv[4], 'points' => $oneQuizRowCsv[5]];
        }        
        
        $questionsArr = [];
        
        foreach($quizArr as $question => $questionDitails) {
            $exist = DB::table('quize_questions')->where('question', $question)->first();

            $insertQuestion = ['discipline_id' => $questionDitails['discipline_id'],
                               'question'      => $question,
                               'teacher_id'    => 0,
                               'total_points'  => 1,
                               'lang'          => $questionDitails['lang']
                            ];
            if(!$exist) {
                 $questionsId = DB::table('quize_questions')->insertGetId($insertQuestion);
                 
                 if ($questionsId) {          
                     
                     $questionAnswers = $quizAnswerArr[$question]['answer'];
                    
                     foreach($questionAnswers as $one) {
                        $correct = 0;
                        $points  = 0;
                         
                        if (!empty($one['correct'])) {
                           $correct = $one['correct'];
                        } 
                         
                        if (!empty($one['points'])) {
                            $points = $one['points'];
                        } 
                         
                        $insertAnswer = ['question_id' => $questionsId,
                                         'answer'      =>   $one['answer'],
                                         'correct'     => $correct,
                                         'points'      => $points
                                        ];
                         
                        DB::table('quize_answers')->insert($insertAnswer);                            
                         
                    }
                    
                }
            }
            $this->output->progressAdvance();
        }
        $this->output->progressFinish();
    }
}
