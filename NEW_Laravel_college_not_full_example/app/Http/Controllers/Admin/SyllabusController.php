<?php

namespace App\Http\Controllers\Admin;

use App\Discipline;
use App\QuizAnswer;
use App\QuizeAudiofile;
use App\QuizQuestion;
use App\Services\{Auth, HtmlHelper, SyllabusService};
use App\Syllabus;
use App\SyllabusDocument;
use App\SyllabusQuizeQuestion;
use App\StudentDiscipline;
use App\{Models\StudentDisciplineDayLimit,
    Semester,
    StudyGroup,
    StudyGroupTeacher,
    SyllabusModule,
    SyllabusTask,
    SyllabusTaskAnswer,
    SyllabusTaskQuestions};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{App,DB,Log,Response,Session,Validator};
use App\Services\SyllabusDetailDoc;
use Illuminate\Validation\Rule;
use App\Validators\AdminSyllabusEditValidator;
use App\DisciplinePracticeDocument;
use App\SyllabusLiterature;
use App\LibraryCatalogDiscipline;



class SyllabusController extends Controller
{
    /**
     * @param $disciplineId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList($disciplineId, Request $request)
    {
        $oDiscipline = Discipline
            ::with(['studentDisciplineDayLimits' => function($query){
                $query->where('teacher_id', Auth::user()->id);
            }])
            ->where('id', $disciplineId)
            ->first();

        $syllabusList = Syllabus::getListForAdmin($disciplineId);
        $syllabusModules = SyllabusModule::where('discipline_id', $disciplineId)->get();

        $adminSyllabusLang = Session::get('admin_syllabus_lang', 'ru');
        $syllabusDocuments = DisciplinePracticeDocument::where('discipline_id', $disciplineId)->get();

        $studyGroupList = StudyGroupTeacher
            ::select([
                'study_groups.id as id',
                'study_groups.name as name'
            ])
            ->leftJoin('study_groups', 'study_groups.id', '=', 'study_group_teacher.study_group_id')
            ->where('study_group_teacher.discipline_id', $disciplineId)
            ->where('user_id', Auth::user()->id)
            ->get();

        $syllabus = new Syllabus();
        
        if (!$syllabus) {
            abort(404);
        }
        
        if(count($syllabusList) > 0) { // todo надо подумать -- для создания опроса требуется экземплят обьекта темы. 
            $themeIdQuizVoid = $syllabusList[0]->id; // пока будет служить болванкой для опросов.        
        } else {
            $themeIdQuizVoid = 0;
        }

        return view('admin.pages.syllabus.list', [
            'syllabusList'      => $syllabusList,
            'disciplineId'      => $disciplineId,
            'defaultLang'       => $request->input('defaultLang', $adminSyllabusLang),
            'oDiscipline'       => $oDiscipline,
            'syllabusModules'   => $syllabusModules,
            'syllabusDocuments' => $syllabusDocuments,
            'studyGroupList'    => $studyGroupList,
            'syllabus'          => $syllabus,
            'quizeQuestionList' => $syllabus->quizeQuestions,
            'themeIdQuizVoid'   => $themeIdQuizVoid
        ]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($disciplineId, $themeId, $language = null)
    {
        $oDiscipline = Discipline::where('id', $disciplineId)->first();

        $syllabus = $themeId == 'add' ? new Syllabus() : Syllabus::getByIdForAdmin($disciplineId, $themeId);

        if (!$syllabus) {
            abort(404);
        }

        $mainLiterature = $themeId != 'add' ? $syllabus->literature()->where('syllabus_literatures.literature_type', 'main')->get() : [];
        $secondaryLiterature = $themeId != 'add' ? $syllabus->literature()->where('syllabus_literatures.literature_type', 'secondary')->get() : [];

        if ($syllabus->id) {
            Session::put('admin_syllabus_lang', $syllabus->language);

            $syllabusModules = SyllabusModule::
                                where('discipline_id', $disciplineId)->
                                where('language', $syllabus->language)->
                                get();
        } else {
            Session::put('admin_syllabus_lang', $language);

            $syllabusModules = SyllabusModule::
                                where('discipline_id', $disciplineId)->
                                where('language', $language)->
                                get();
        }

        // get task data
        $aTask = SyllabusTask::getTaskData( $syllabus->id );

        // count discipline hours
        $iAllHours = 0;
        $iAllHours += $syllabus->contact_hours ?? 0;
        $iAllHours += $syllabus->self_hours ?? 0;
        $iAllHours += $syllabus->self_with_teacher_hours ?? 0;
        $iAllHours += $syllabus->srop_hours ?? 0;
        $iAllHours += $syllabus->sro_hours ?? 0;


        return view('admin.pages.syllabus.form.main', [
            'syllabus'          => $syllabus,
            'quizeQuestionList' => $syllabus->quizeQuestions,
            'disciplineId' => $disciplineId,
            'language' => $language,
            'oDiscipline' => $oDiscipline,
            'task'        => $aTask,
            'allHours' => $iAllHours,
            'syllabusModules'   => $syllabusModules,
            'mainLiterature' => $mainLiterature,
            'secondaryLiterature' => $secondaryLiterature
        ]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function editPost($disciplineId, $themeId, Request $request)
    {
        // validation data
        $obValidator = AdminSyllabusEditValidator::make($request->all());
        if ($obValidator->fails()) {
            return redirect()->back()->withErrors($obValidator->errors());
        }

        if( $themeId == 'add' )
        {
            $syllabus = new Syllabus();
        } else {
            $syllabus = Syllabus::getByIdForAdmin( intval($disciplineId), intval($themeId) );
        }

        $disciplineId = intval( $disciplineId );
        $themeId      = intval( $themeId );

        $syllabus->discipline_id = $disciplineId;

        if (!$syllabus) {
            abort(404);
        }

        $data = $request->all();
        $data['for_test1'] = !empty($data['for_test1']);
        unset($data['literature']);
        unset($data['literature_added']);

        $syllabus->fill($data);
        $syllabus->save();
        SyllabusService::recalculationSyllabusStatus($disciplineId);

        SyllabusLiterature::where('syllabus_id', $syllabus->id)->delete();
        foreach ($request->literature as $value) {
            SyllabusLiterature::create([
                'syllabus_id' => $syllabus->id,
                'literature_id' => $value,
                'literature_type' => 'main'
            ]);
            LibraryCatalogDiscipline::updateOrCreate([
                'literature_catalog_id' => $value,
                'discipline_id' => intval($disciplineId)
            ]);
        }

        if(isset($request->literature_added)){
            foreach ($request->literature_added as $value) {
                SyllabusLiterature::create([
                    'syllabus_id' => $syllabus->id,
                    'literature_id' => $value,
                    'literature_type' => 'secondary'
                ]);
                LibraryCatalogDiscipline::updateOrCreate([
                    'literature_catalog_id' => $value,
                    'discipline_id' => intval($disciplineId)
                ]);
            }
        }

        $syllabus->attachMaterials(
            $request->input('practicalMaterials'),
            $request->file('practicalMaterials'),
            SyllabusDocument::MATERIAL_TYPE_PRACTICAL,
            $syllabus->language
        );
        $syllabus->attachMaterials(
            $request->input('teoreticalMaterials'),
            $request->file('teoreticalMaterials'),
            SyllabusDocument::MATERIAL_TYPE_TEORETICAL,
            $syllabus->language
        );
        $syllabus->attachMaterials(
            $request->input('sroMaterials'),
            $request->file('sroMaterials'),
            SyllabusDocument::MATERIAL_TYPE_SRO,
            $syllabus->language
        );
        $syllabus->attachMaterials(
            $request->input('sropMaterials'),
            $request->file('sropMaterials'),
            SyllabusDocument::MATERIAL_TYPE_SROP,
            $syllabus->language
        );

        StudentDiscipline
            ::where('discipline_id', $disciplineId)
            ->update(['syllabus_updated' => 1]);

        return redirect()->route('adminSyllabusEdit', [
            'disciplineId' => $disciplineId,
            'themeId' => $syllabus->id
        ]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editQuizePost($disciplineId, $themeId, Request $request)
    {
        $syllabus = $themeId == 'add' ? null : Syllabus::getByIdForAdmin($disciplineId, $themeId);

        if (!$syllabus) {
            return redirect()->back()->withErrors(['Необходимо заполнить и сохранить описание силлабуса']);
        }

        $syllabus->discipline_id = $disciplineId;

        if (!$syllabus) {
            abort(404);
        }

        $syllabus->attachQuizeQuestions($request->input('questions'), $request->file('questions'));
        $syllabus->save();

        return redirect()->route('adminSyllabusEdit', [
            'disciplineId' => $disciplineId,
            'themeId' => $syllabus->id
        ]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function saveQuizQuestion($disciplineId, $themeId, Request $request)
    {
        if (!$request->input('answers')) {
            abort('404');
        }

        $syllabus = $themeId == 'add' ? null : Syllabus::getByIdForAdmin($disciplineId, $themeId);

        if (!$syllabus) {
            return Response::json(['error' => 'syllabus not found']);
        }

        $audio = $request->input('audiofiles');
        if (is_array($audio) && count($audio) == 0) {
            $fileList = QuizeAudiofile::where('quize_question_id', $request->input('id'))->get();
            foreach ($fileList as $file) {
                \File::delete(public_path('audio/' . $file->filename));
                $file->delete();
            }
        }
        $syllabus->attachSingleQuestion($request->all());
        $syllabus->discipline->updateQuestionIndex();

        return Response::json([]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @param Request $request
     */
    public function quizList($disciplineId, $themeId, Request $request)
    {
        $questions = QuizQuestion
            ::select([
                'quize_questions.id as id',
                'quize_questions.question as question',
                'quize_questions.lang as lang'
            ])
            ->leftJoin('syllabus_quize_questions', 'syllabus_quize_questions.quize_question_id', '=', 'quize_questions.id')
     //       ->where('syllabus_quize_questions.syllabus_id', $themeId)
            ->where('discipline_id', $disciplineId)
            ->get();

        foreach ($questions as $k => $question) {
           $questions[$k]->question = HtmlHelper::stripTag(['table', 'br'], $question->question);
        }
        
        

        return Response::json($questions);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQuizePost(Request $request)
    {
        if (!$request->input('id')) {
            return Response::json(['error' => 'Question id not found']);
        }

        $forDelete = SyllabusQuizeQuestion
            ::where('quize_question_id', $request->input('id'))
            ->first();

        if ($forDelete) {
            $syllabus = Syllabus::where('id', $forDelete->syllabus_id)->first();
            $forDelete->deleteWithQuestion();
            $syllabus->discipline->updateQuestionIndex();
        }

        return Response::json([]);
    }

    /**
     * @param $disciplineId
     * @param $themeId
     */
    public function delete($disciplineId, $themeId)
    {
        Syllabus
            ::where('discipline_id', $disciplineId)
            ->where('id', $themeId)
            ->delete();

        $discipline = Discipline::where('id', $disciplineId)->first();
        $discipline->updateQuestionIndex();

        return redirect()->route('adminSyllabusList', [
            'disciplineId' => $disciplineId
        ]);
    }

    /**
     * @param Request $request
     * @param $disciplineId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function exportPdf($disciplineId, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lang' => ['required', Rule::in(['ru', 'kz', 'en'])]
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $discipline = Discipline::where('id', $disciplineId)->first();

        if (!$discipline) {
            return redirect()->back()->withErrors(['Дисциплина не найдена']);
        }

        $themeList = Syllabus
            ::where('discipline_id', $disciplineId)
            ->where('language', $request->input('lang'))
            ->get();

        if (!$themeList) {
            return redirect()->back()->withErrors(['Список тем пуст']);
        }
        ini_set('max_execution_time', 900);

        $template = 'admin.pages.syllabus.export.html';
        $fileName = 'themes.pdf';

        if ($request->input('mode') && $request->input('mode') == 'question_only') {
            $template = 'admin.pages.syllabus.export.questionOnly';
        }

        $pdf = App::make('dompdf.wrapper');
        $pdf->loadView($template, compact('themeList', 'discipline'));

        return $pdf->download($fileName);
    }

    public function exportDoc($disciplineId, Request $request){
        $validator = Validator::make($request->all(), [
            'lang' => ['required', Rule::in(['ru', 'kz', 'en'])]
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        return SyllabusDetailDoc::create($disciplineId, $request->input('lang', 'ru'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function questionInfo(Request $request)
    {
        $question = QuizQuestion
            ::with(['answers' => function ($query) {
                $query->select([
                    'id',
                    'question_id',
                    'answer',
                    'points',
                    'correct'
                ]);
            }])
            ->with(['audiofiles' => function ($query) {
                $query->select([
                    'quize_question_id',
                    'filename'
                ]);
            }])
            ->select([
                'id',
                'question'
            ])
            ->where('id', $request->id)
            ->first();

        return Response::json($question);
    }

    /**
     * @param $disciplineId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function copyToLang($disciplineId, Request $request)
    {
        $newLang = $request->input('lang');
        if (!in_array($newLang, ['ru', 'kz', 'en'])) {
            return Response::json(['error' => 'lang not found']);
        }

        if (!is_array($request->input('idList'))) {
            return Response::json(['error' => 'array of themes not found']);
        }

        foreach ($request->input('idList') as $id) {
            $syllabus = Syllabus::where('id', $id)->first();

            if (!$syllabus) {
                continue;
            }

            /*Copy syllabus with new lang*/
            $newSyllabus = new Syllabus();
            $newSyllabus->fill([
                'language' => $request->input('lang'),
                'theme_number' => $syllabus->theme_number,
                'theme_name' => $syllabus->theme_name,
                'literature' => $syllabus->literature,
                'contact_hours' => $syllabus->contact_hours,
                'self_hours' => $syllabus->self_hours,
                'self_with_teacher_hours' => $syllabus->self_with_teacher_hours,
                'sro_hours' => $syllabus->sro_hours,
                'srop_hours' => $syllabus->srop_hours,
                'literature_added' => $syllabus->literature_added,
                'teoretical_description' => $syllabus->teoretical_description,
                'practical_description' => $syllabus->practical_description,
                'sro_description' => $syllabus->sro_description,
                'srop_description' => $syllabus->srop_description,

            ]);
            $newSyllabus->discipline_id = $syllabus->discipline_id;
            $newSyllabus->save();

            SyllabusService::recalculationSyllabusStatus( $syllabus->discipline_id );

            /*Copy documents*/
            $documents = $syllabus->documents;

            foreach ($documents as $document) {
                $newDocument = new SyllabusDocument();
                $newDocument->syllabus_id = $newSyllabus->id;
                $newDocument->lang = $newLang;
                $newDocument->resource_type = $document->resource_type;
                $newDocument->material_type = $document->material_type;
                $newDocument->filename = $document->filename;
                $newDocument->filename_original = $document->filename_original;
                $newDocument->link = $document->link;
                $newDocument->document_type = $document->document_type;
                $newDocument->save();
            }

            /*Copy Questions*/
            $questions = $syllabus->quizeQuestions;

            $newQuestionList = [];
            foreach ($questions as $question) {
                $newQuestion = new QuizQuestion();
                $newQuestion->discipline_id = $question->discipline_id;
                $newQuestion->question = $question->question;
                $newQuestion->teacher_id = $question->teacher_id;
                $newQuestion->total_points = $question->total_points;
                $newQuestion->save();
                $newQuestionList[] = $newQuestion->id;

                /*Copy Audiofiles*/
                $audios = $question->audiofiles;
                $newAudiobList = [];
                foreach ($audios as $audio) {
                    $newAudiobList[] = [
                        'quize_question_id' => $newQuestion->id,
                        'filename' => $audio->filename,
                        'original_filename' => $audio->original_filename,
                        'created_at' => DB::raw('now()')
                    ];
                }
                QuizeAudiofile::insert($newAudiobList);

                /*Copy answers*/
                $answers = $question->answers;
                $newAnswerList = [];
                foreach ($answers as $answer) {
                    $newAnswerList[] = [
                        'question_id' => $newQuestion->id,
                        'answer' => $answer->answer,
                        'points' => $answer->points,
                        'correct' => $answer->correct,
                        'img' => $answer->img,
                        'created_at' => DB::raw('now()')
                    ];
                }
                QuizAnswer::insert($newAnswerList);
            }

            $newSyllabus->quizeQuestions()->sync($newQuestionList);

            $syllabus->discipline->updateQuestionIndex();
        }

        return Response::json([]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveToModule(Request $request) {
        $themeList = $request->input('theme_list', null);

        if (!is_array($themeList)) {
            return Response::json(['error' => 'array of themes not found']);
        }

        Syllabus::whereIn('id', $themeList)->update([
            'module_id' => $request->input('module_id', null),
        ]);

        return Response::json([
            'status' => 'success',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function setTest1(Request $request)
    {
        if (empty($request->input('syllabusId'))) {
            abort('404');
        }

        Syllabus::setTest1($request->input('syllabusId'), $request->input('on'));

        return Response::json([]);
    }

    public function getDocumentsList($disciplineId)
    {
        $sortDocuments = ['ru' => [], 'en' => [], 'kz' => []];
        $syllabusDocuments = DisciplinePracticeDocument::where('discipline_id', $disciplineId)->get();

        foreach($syllabusDocuments as $doc){
            $document =  $doc;
            $document['url'] = $doc->getPublicUrl();
            $sortDocuments[$doc->lang][] = $doc;
        }

        return response()->json($sortDocuments);
    }

    public function addDocument(Request $request, $disciplineId)
    {
        $rule = array(
            'document' => 'required|file|mimes:docx,doc,pdf'
        );
        $validator = Validator::make($request->all(), $rule);

        if ($validator->fails()){
            return response()->json( ['error' => 'Тип документа должен быть: docx, doc, pdf.'], 422);
        }
        if ($request->has('document')){
            $document = DisciplinePracticeDocument::createDocument($disciplineId, $request->all());
        }
        $response = $document;
        $response['url'] = $document->getPublicUrl();
        $response['success'] = 'Документ успешно добавлен.';

        return response()->json($response);
    }

    public function deleteDocument($disciplineId, $id)
    {
        $file = DisciplinePracticeDocument::find($id);
        $file->deleteFile();
        $file->delete();

        return response()->json(['success' => 'Документ успешно удален.']);
    }

    public function updateThemeHours($disciplineId, Request $request) {
        $syllabus = Syllabus::getByIdForAdmin($disciplineId, $request->input('theme_id', 0));

        $syllabus->fill([
            $request->input('key', '') => $request->input('value', '')
        ]);
        $syllabus->save();

        return Response::json([]);
    }

    public function editDescription($disciplineId, $lang, Request $request)
    {
        $langs = ['en', 'ru', 'kz'];
        $validator = Validator::make($request->all(), [
            'description' => 'string|required',
        ]);

        if ($validator->fails() and in_array($lang, $langs)){

            return response()->json(['error' => 'Описание не может быть пустым.'], 422);
        }
    
        $discipline = Discipline::where('id', $disciplineId)->first();
       
        $discipline->{'files_description_'.$lang} = $request->get('description');

        $discipline->save();

        return response()->json(['success' => 'Описание успешно изменено.']);
    }

    public function ratingLimitUpdate(Request $request, $disciplineId)
    {
        $ratingLimits = $request->input('rating_list');
        $groupId = $request->input('group_id');

        foreach($ratingLimits as $day => $ratingLimit)
        {
            $studentDisciplineLimit = StudentDisciplineDayLimit
                ::where('day_num', $day)
                ->where('discipline_id', $disciplineId)
                ->where('study_group_id', $groupId)
                ->first();

            if($ratingLimit)
            {
                if (!$studentDisciplineLimit) {
                    $studentDisciplineLimit = new StudentDisciplineDayLimit();
                    $studentDisciplineLimit->discipline_id = $disciplineId;
                    $studentDisciplineLimit->day_num = $day;
                    $studentDisciplineLimit->study_group_id = $groupId;
                }

                $studentDisciplineLimit->rating_limit = $ratingLimit;

                if( !in_array(Auth::user()->id, StudentDisciplineDayLimit::ACCESS_TO_ADMIN_LIST))
                {
                    $studentDisciplineLimit->teacher_id = Auth::user()->id;
                }

                $studentDisciplineLimit->save();
            }
            else
            {
                if($studentDisciplineLimit)
                {
                    $studentDisciplineLimit->delete();
                }
            }
        }

    }

    /**
     * @param Request $request
     * @param $disciplineId
     * @return array
     */
    public function getDayRatingList(Request $request, $disciplineId)
    {
        $groupId = $request->input('group_id');

        $dayRatingList = StudentDisciplineDayLimit
            ::where('discipline_id', $disciplineId)
            ->where('study_group_id', $groupId);

        if( !in_array(Auth::user()->id, StudentDisciplineDayLimit::ACCESS_TO_ADMIN_LIST))
        {
            $dayRatingList->where('teacher_id', Auth::user()->id);
        }

        $dayRatingList = $dayRatingList->get();
        $resultList = [];

        foreach($dayRatingList as $dayRating)
        {
            $resultList[$dayRating->day_num] = $dayRating->rating_limit;
        }

        return $resultList;
    }
}
