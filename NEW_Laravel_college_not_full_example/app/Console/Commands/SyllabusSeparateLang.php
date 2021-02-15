<?php

namespace App\Console\Commands;

use App\QuizAnswer;
use App\QuizeAudiofile;
use App\QuizQuestion;
use App\Syllabus;
use App\SyllabusDocument;
use App\SyllabusQuizeQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyllabusSeparateLang extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syllabus:separate:lang';

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
     * @param $syllabus
     */
    private function detectLangs($syllabus)
    {
        $langs = [];

        if($syllabus->theme_name !== null && strlen($syllabus->theme_name) > 0)
        {
            $langs[] = 'ru';
        }

//        if($syllabus->theme_name_en !== null && strlen($syllabus->theme_name_en) > 0)
//        {
//            $langs[] = 'en';
//        }
//
//        if($syllabus->theme_name_kz !== null && strlen($syllabus->theme_name_kz) > 0)
//        {
//            $langs[] = 'kz';
//        }

        return $langs;
    }

    /**
     * @param $parentAnswer
     * @param $suffix
     * @param $question
     * @return QuizQuestion|null
     */
    private function createAnswerByLang($parentAnswer, $suffix, $question)
    {
        $answer = null;

        if($parentAnswer['answer' . $suffix])
        {
            $answer = new QuizAnswer();

            $answer->fill([
                'answer'        => $parentAnswer['answer' . $suffix],
                'points'        => $parentAnswer->points,
                'correct'       => $parentAnswer->correct
            ]);
            $answer->question_id = $question->id;
            $answer->save();
            Log::info('Create answer', ['answer' => $answer]);
        }

        return $answer;
    }

    /**
     * @param $parentQuestion
     * @param $suffix
     * @return QuizQuestion|null
     */
    private function createQuestionByLang($parentQuestion, $suffix)
    {
        $question = null;

        if($parentQuestion['question' . $suffix])
        {
            $question = new QuizQuestion();

            $question->question = $parentQuestion['question' . $suffix];
            $question->discipline_id = $parentQuestion->discipline_id;
            $question->teacher_id = $parentQuestion->teacher_id;
            $question->total_points = $parentQuestion->total_points;
            $question->save();
            Log::info('Create question', ['question' => $question]);
            $answers = $parentQuestion->answers;

            /*copy audio*/
            $audiofiles = $parentQuestion->audiofiles;
            $newAudiobList = [];

            foreach ($audiofiles as $audio) {
                $newAudiobList[] = [
                    'quize_question_id' => $question->id,
                    'filename' => $audio->filename,
                    'original_filename' => $audio->original_filename,
                    'created_at'    => DB::raw('now()')
                ];
            }

            QuizeAudiofile::insert($newAudiobList);

            /*create answers*/
            foreach ($answers as $answer)
            {
                $this->createAnswerByLang($answer, $suffix, $question);
            }
        }

        return $question;
    }

    /**
     * @param $syllabus
     * @param $lang
     */
    private function createSyllabusByLang($syllabus, $lang)
    {
        $langsForCreate = in_array($lang, ['ru', 'kz', 'en']) ? [$lang] : ['ru', 'kz'];

        foreach ($langsForCreate as $langItem)
        {
            $suffix = '';
            if($langItem != 'ru')
            {
                $suffix = '_' . $langItem;
            }

            $newSyllabus = new Syllabus();
            $newSyllabus->fill([
                'language'                  => $langItem,
                'theme_number'              => $syllabus['theme_number' . $suffix],
                'theme_name'                => $syllabus['theme_name' . $suffix],
                'literature'                => $syllabus['literature' . $suffix],
                'contact_hours'             => $syllabus->contact_hours,
                'self_hours'                => $syllabus->self_hours,
                'self_with_teacher_hours'   => $syllabus->self_with_teacher_hours
            ]);
            $newSyllabus->discipline_id = $syllabus->discipline_id;

            $newSyllabus->save();
            Log::info('Create syllabus', ['syllabus' => $newSyllabus]);
            $questions = $syllabus->quizeQuestions;

            /*reattach documents*/
            SyllabusDocument
                ::where('syllabus_id', $syllabus->id)
                ->where('lang', $langItem)
                ->update(['syllabus_id' => $newSyllabus->id]);

            /*Create questions*/
            foreach ($questions as $question)
            {
                $newQuestion = $this->createQuestionByLang($question, $suffix);

                if($newQuestion)
                {
                    SyllabusQuizeQuestion::insert([
                        'syllabus_id' => $newSyllabus->id,
                        'quize_question_id' => $newQuestion->id,
                        'created_at'   => DB::raw('now()')
                    ]);
                }
            }
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$syllabusList = Syllabus::where('id', 48)->get();
        $syllabusList = Syllabus::get();

        foreach ($syllabusList as $syllabus)
        {
            $langs = $this->detectLangs($syllabus);
            $countLangs = count($langs);

            Log::info('Syllabus lang', ['syllabus' => $syllabus, 'langs' => $langs]);

            if($countLangs > 0)
            {
                $syllabus->language = $langs[0];

                if($langs[0] != 'ru')
                {
                    $syllabus->theme_number = $syllabus['theme_number_' . $langs[0]];
                    $syllabus->theme_name = $syllabus['theme_name_' . $langs[0]];
                    $syllabus->literature = $syllabus['literature_' . $langs[0]];

                    $questions = $syllabus->quizeQuestions;

                    foreach($questions as $question)
                    {
                        $question->question = $question['question_' . $langs[0]];
                        $question->save();

                        $answers = $question->answers;

                        foreach ($answers as $answer)
                        {
                            $answer->answer = $answer['answer_' . $langs[0]];
                            $answer->save();
                        }
                    }
                }
                $syllabus->save();

                if($countLangs > 1)
                {
                    for($i=1; $i< $countLangs; $i++)
                    {
                        $this->createSyllabusByLang($syllabus, $langs[$i]);
                    }
                }
            }
        }
    }
}
