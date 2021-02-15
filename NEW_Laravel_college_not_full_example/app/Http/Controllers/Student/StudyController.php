<?php

namespace App\Http\Controllers\Student;

use Auth;
use App;
use App\{
    Discipline,
    DisciplinePayCancel,
    FinanceNomenclature,
    Mail\StudentNotConfirmStudyPlan,
    Profiles,
    Semester,
    Services\SearchCache,
    StudentFinanceNomenclature,
    StudentDiscipline,
    StudentPracticeFiles,
    Language,
    StudentSubmodule,
    Speciality,
    Services\Service1C,
    StudentPracticeDocuments
};
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use App\Validators\{ProfileStudyAjaxBuyServiceValidator, StudyAjaxPrintBuyServiceValidator};
use Illuminate\Http\Request;
use Bnb\GoogleCloudPrint\Facades\GoogleCloudPrint;
use Validator;

class StudyController extends Controller
{
    const ENQUIRE_NAME_TR    = 'Transcript enquire';
    const ENQUIRE_NAME_GCVP4  = 'Reference to the state pension payment center type 4';
    const ENQUIRE_NAME_GCVP21  = 'Reference to the state pension payment center type 21';
    const ENQUIRE_NAME_GCVP6  = 'Reference to the state pension payment center type 6';
    const ENQUIRE_NAME_GR    = 'Reference to the military commissariat';
    const ENQUIRE_NAME_ENTER = 'Reference for submission upon request';

    /**
     * Page "Study"
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        if (empty(Auth::user()->id) || empty(Auth::user()->studentProfile)) {
            abort(404);
        }
dumpLog(Auth::user()->id);
        $currentSemesterString = Semester::current(Auth::user()->studentProfile->education_study_form);

        // Get student's disciplines
        $studentDisciplines = StudentDiscipline::getDataForStudyPage(Auth::user()->id, false);
        //Log::info('$studentDisciplines: ' . var_export($studentDisciplines,true));

        // Get submodules
        $submodules = StudentSubmodule::getForStudyPage(Auth::user()->id, Auth::user()->studentProfile->education_speciality_id ?? 0);

        // Combine and Sort by semester
        $studentDisciplines = StudentDiscipline::combineAndSortSubmodulesAndDisciplines($submodules, $studentDisciplines);

        unset($submodules);

        // Check for available
        $studentDisciplines = StudentDiscipline::checkSubmodulesAndDisciplinesForAvailable(
            Auth::user(),
            $studentDisciplines,
            $currentSemesterString
        );

        // Для первого курса
        if (Auth::user()->study_year == 1) {
            // Get elective disciplines
            if (!empty(Auth::user()->studentProfile->elective_speciality_id)) {
                $electiveSpecialities = [];

                $electiveDisciplines = StudentDiscipline::getDataForStudyPage(Auth::user()->id, true);

                // Check elective for available
                $electiveDisciplines = StudentDiscipline::checkSubmodulesAndDisciplinesForAvailable(
                    Auth::user(),
                    $electiveDisciplines,
                    $currentSemesterString
                );
            } else {
                $electiveSpecialities = Speciality::getElective(Auth::user()->studentProfile->speciality->code_char, Auth::user()->studentProfile->education_speciality_id, Auth::user()->studentProfile->speciality->year);

                $electiveDisciplines = new Collection();
            }
        } else {
            $electiveSpecialities = [];
            $electiveDisciplines  = new Collection();
        }

        //$courseList = Course::getListForShopOther();

        $nomenclature = FinanceNomenclature::getForStudyPage(Auth::user()->studentProfile->category);
        $boughtServiceIds = StudentFinanceNomenclature::getBoughtServiceIds(Auth::user()->id, Auth::user()->studentProfile->currentSemester());

        $locale = Language::getFieldName('name', app()->getLocale());

        // For transit students
        $transitClassAttendanceBought = false;
        if (Auth::user()->studentProfile->category == Profiles::CATEGORY_TRANSIT) {
            $transitClassAttendanceBought = StudentFinanceNomenclature::isBought(Auth::user()->id, FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID, Auth::user()->studentProfile->currentSemester());
        }

        $notConfirmedPlanDisciplines = StudentDiscipline::getNotConfirmed(Auth::user()->id);

        $gpa = Auth::user()->gpa;

        $buyEnabled = Auth::user()->studentProfile->buying_allow;
//        $buyEnabled = false;

        $cancelPayDisciplineIdList = DisciplinePayCancel::getDisciplineArray(Auth::user()->id);

        return view('student.study.index', compact(
            'studentDisciplines',
            'locale',
            'nomenclature',
            'electiveSpecialities',
            'electiveDisciplines',
            'transitClassAttendanceBought',
            'boughtServiceIds',
            'buyEnabled',
            'cancelPayDisciplineIdList',
            'gpa',
            'notConfirmedPlanDisciplines',
            'currentSemesterString'
        ));
    }

    /**
     * Choose elective speciality
     * @param Request $request
     * @return array
     */
    public function setElectiveSpeciality(Request $request)
    {
        // Already set
        if (!empty(Auth::user()->studentProfile->elective_speciality_id)) {
            return redirect()->route('study');
        }

        if (empty($request->input('elective_speciality_id'))) {
            abort(500, 'Need POST specialityId');
        }

        Profiles::setElectiveSpecialityId(Auth::user()->id, $request->input('elective_speciality_id'));
        Auth::user()->fresh();

        StudentDiscipline::addElectiveDisciplines(Auth::user()->id, $request->input('elective_speciality_id'));

        return redirect()->route('study');
    }


    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function renderPrintBuyService( Request $request )
    {

        // validation data
        $obValidator = StudyAjaxPrintBuyServiceValidator::make($request->all());
        if ( $obValidator->fails() || empty(Auth::user()) ) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        //$OUser    = Auth::user();
        //$oProfile = Auth::user()->studentProfile;
        $oFinanceNomenclature = FinanceNomenclature::
        where('id',$request->input('service'))->
        first();
        if( empty($oFinanceNomenclature) )
        {
            return \Response::json([
                'status' => false,
                'message' => __('Data not found')
            ]);
        }

        return view('pdf.buy_service', [
            'service' => $oFinanceNomenclature
        ]);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function ajaxPrintBuyService( Request $request )
    {

        // validation data
        $obValidator = StudyAjaxPrintBuyServiceValidator::make($request->all());
        if ($obValidator->fails()) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        //$OUser    = Auth::user();
        //$oProfile = Auth::user()->studentProfile;
        $oFinanceNomenclature = FinanceNomenclature::
        where('id',$request->input('service'))->
        first();
        if( empty($oFinanceNomenclature) )
        {
            return \Response::json([
                'status' => false,
                'message' => __('Data not found')
            ]);
        }

        try {

            GoogleCloudPrint::asHtml()
                ->url( route('renderPrintBuyService',[ 'service' => $request->input('service') ]) )
                ->printer(env('GCP_PRINTER_ID'))
                ->send();

        } catch( \Exception $e ){

            return \Response::json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }


        return \Response::json([
            'status' => true,
            'message' => __('Success')
        ]);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxUserBuyService(Request $request)
    {
        $return = [
            'status' => true,
            'message' => __('Success buy service')
        ];

        // validation data
        $obValidator = ProfileStudyAjaxBuyServiceValidator::make($request->all());
        if ($obValidator->fails()) {
            return \Response::json([
                'status' => false,
                'message' => __('Error input data')
            ]);
        }

        $oUser = Auth::user();

        $service = FinanceNomenclature::getById($request->input('service'));

        if (!empty($oUser->studentProfile) && !empty($service)) {
            if ($oUser->balance < $service->cost) {
                return Response::json([
                    'status' => false,
                    'message' => __('Not enough funds on balance')
                ]);
            }

            // Limit 1
            if ($service->only_one && StudentFinanceNomenclature::isBought(Auth::user()->id, $service->id)) {
                return Response::json([
                    'status' => false,
                    'message' => __('This service can only be bought once')
                ]);
            }

            // Limit 1 per semester
            if ($service->only_one_per_semester && StudentFinanceNomenclature::isBought(Auth::user()->id, $service->id, Auth::user()->studentProfile->currentSemester())) {
                return Response::json([
                    'status' => false,
                    'message' => __('This service can only be bought once per semester')
                ]);
            }

            $balanceBeforeCall = Auth::user()->balance;

            $mResponse = Service1C::pay(
                $oUser->studentProfile->iin,
                $service->code,
                $service->cost
            );

            // Successfully paid
            if ($mResponse) {
                // Add log
                StudentFinanceNomenclature::add(Auth::user()->id, $service, Auth::user()->studentProfile->currentSemester(), $balanceBeforeCall);

                $pdf = false;

                if ($service->name_en == self::ENQUIRE_NAME_TR) {
                    $pdf = DocsController::genTranscript();
                } elseif ($service->name_en == self::ENQUIRE_NAME_GCVP4) {
                    $pdf = DocsController::genGcvp4();
                } elseif ($service->name_en == self::ENQUIRE_NAME_GCVP21) {
                    $pdf = DocsController::genGcvp21();
                } elseif ($service->name_en == self::ENQUIRE_NAME_GCVP6) {
                    $pdf = DocsController::genGcvp6();
                } elseif ($service->name_en == self::ENQUIRE_NAME_GR) {
                    $pdf = DocsController::genGraduate();
                } elseif ($service->name_en == self::ENQUIRE_NAME_ENTER) {
                    $pdf = DocsController::genEntered();
                }

                if ($pdf != false) {
                    $pdf = json_decode($pdf);
                    if($pdf->Transcript) {
                        $return['message'] .= '. ' . __('File has been generated successfully you can download it by') . '<a target="_blank" href=' . $pdf['filename'] . '>' . __('next URL') . '</a>';
                    } else {
                        $return['message'] .= '. ' . __("The file was successfully created and printed, you can pick it up within 3 working days at the Registrar's Office. Your document number") .' <strong>' . $pdf->dailyFullId . '</strong>. ';
                    }
                    
                }

                $mailSubject = ($request->input('service') == FinanceNomenclature::TRANSIT_CLASS_ATTENDANCE_ID) ? 'Доступ к посещению занятий, для студентов иностранных ВУЗов-партнеров' : 'Была купленна новая услуга';

                // send report on the buy of the service user
                Mail::send('emails.buy_service_user', [
                        'user_id' => $oUser->id,
                        'fio' => $oUser->studentProfile->fio ?? $oUser->name,
                        'service_code' => $service->code,
                        'service_name' => $service->name,
                        'cost' => $service->cost,
                        'date' => date('d-m-Y H:i'),
                        'enquireURL' => $pdf->filename ?? ''
                    ], function ($message) use ($mailSubject) {
                        $message->from(getcong('site_email'), getcong('site_name'));
                        $message->to(explode(',', env('MAIL_FOR_ERROR_REPORT_FORM_OFFICE')))->subject($mailSubject);
                    });

                return \Response::json($return);
            }
        }

        return \Response::json([
            'status' => false,
            'message' => __('Request error')
        ]);
    }

    public function disciplinePayCancel(Request $request)
    {
        /** @var StudentDiscipline $SD */
        $SD = StudentDiscipline::where('student_id', \App\Services\Auth::user()->id)
            ->where('discipline_id', $request->input('discipline_id'))
            ->first();

        $cancelPayDisciplineIdList = DisciplinePayCancel::getDisciplineArray(Auth::user()->id);
        $SD->setPayCancelButtonShow(Auth::user(), $cancelPayDisciplineIdList);
        if (!$SD->payCancelButtonShow) {
            return Response::json([
                'status' => false,
                'message' => '!payCancelButtonShow'
            ]);
        }

        if(!$SD)
        {
            return Response::json([
                'status' => false,
                'message' => __('Discipline not found')
            ]);
        }

        $cancelPayModel = DisciplinePayCancel::where('user_id', \App\Services\Auth::user()->id)
            ->where('discipline_id', $request->input('discipline_id'))
            ->where('status', DisciplinePayCancel::STATUS_NEW)
            ->first();

        if($cancelPayModel)
        {
            return Response::json([
                'status' => false,
                'message' => __('Cancel payment is already being processed')
            ]);
        }

        $discipline = Discipline::where('id', $request->input('discipline_id'))->first();

        $cancelPayModel = new DisciplinePayCancel();
        $cancelPayModel->discipline_id = $request->input('discipline_id');
        $cancelPayModel->user_id = \App\Services\Auth::user()->id;
        $cancelPayModel->status = DisciplinePayCancel::STATUS_NEW;
        $cancelPayModel->save();
        $cancelPayModel->redisCacheRefresh();

        return Response::json([
            'status' => true
        ]);
    }

    public function confirmPlan()
    {
        $notConfirmedPlanDisciplines = StudentDiscipline::getNotConfirmed(Auth::user()->id);

        foreach ($notConfirmedPlanDisciplines as $SD) {
            if (!$SD->plan_student_confirm) {
                $SD->studentConfirmPlanSemester();
            }
        }
        $this->flash_success('Study plan confirmed');

        return redirect()->route('study');
    }

    public function notConfirmPlan(Request $request)
    {
        if (empty($request->input('reason'))) {
            return Response::json([
                'error' => 'Reason is empty'
            ]);
        }

        Mail::to('duisebaeva_b@miras.edu.kz')->send(new StudentNotConfirmStudyPlan(Auth::user()->id, $request->input('reason')));
        Mail::to('karpova_el@miras.edu.kz')->send(new StudentNotConfirmStudyPlan(Auth::user()->id, $request->input('reason')));

        return Response::json([
            'success' => true
        ]);
    }

    public function documentsPage($discipline_id)
    {
        $discipline = Discipline::where('id', $discipline_id)->first();

        $documents = $discipline->documents->where('lang', App::getLocale());

        $documentsNotFilled = collect();
        $documentsStudentFilledIds = [];

        $documentsStudentFilled = Auth::user()->practiceStudentDocuments
                        ->where('discipline_id', $discipline_id)
                        ->where('lang', App::getLocale());

        foreach ($documentsStudentFilled as $document) {
            $documentsStudentFilledIds[] = $document->document_id;
        }
        foreach ($documents as $document){
            if (!in_array($document->id, $documentsStudentFilledIds)){
                $documentsNotFilled->push($document);
            }
        }
        return view('student.study.documents.list', ['documentsNotFilled' => $documentsNotFilled, 'documentsFilled' => $documentsStudentFilled]);
    }

    public function filesPage($discipline_id)
    {
        $userId = App\Services\Auth::user()->id;

        $discipline = Discipline
            ::with(['files' => function($query) use($userId){
                $query
                    ->select([
                        'id',
                        'user_id',
                        'discipline_id',
                        'type',
                        'file_name',
                        'original_name',
                        'link',
                        'created_at',
                        'updated_at',
                        DB::raw('TIMESTAMPDIFF(SECOND, created_at, NOW()) as seconds_of_create')])
                    ->where('user_id', $userId);
            }])
            ->with(['disciplineFiles' => function($query) use($userId){
                $query
                    ->select([
                        'id',
                        'user_id',
                        'discipline_id',
                        'type',
                        'file_name',
                        'original_name',
                        'link',
                        'created_at',
                        'updated_at',
                        DB::raw('TIMESTAMPDIFF(SECOND, created_at, NOW()) as seconds_of_create')])
                    ->where('user_id', $userId);
            }])
            ->where('id', $discipline_id)->first();

        if($discipline->is_practice)
        {
            $files = $discipline->files;
            /*$files = $discipline
                ->files
                ->select(['students_practice_files.*', DB::raw('TIMESTAMPDIFF(SECOND , updated_at, NOW()) as second_of_create')])
                ->where('user_id', Auth::user()->id);*/
        }
        else
        {
            $files = $discipline->disciplineFiles;
            /*$files = $discipline
                ->disciplineFiles
                ->select(['student_discipline_files.*', DB::raw('TIMESTAMPDIFF(SECOND , updated_at, NOW()) as second_of_create')])
                ->where('user_id', Auth::user()->id);*/
        }

        return view('student.study.files.list', [
            'files' => $files,
            'discipline' => $discipline,
        ]);
    }

    public function uploadDocument(Request $request, $discipline_id, $document_id)
    {
        $validator = Validator::make($request->all(), [
           'document' => 'required|file|mimes:doc,docx,pdf'
        ]);
        if ($validator->fails()){
            return redirect()->back()->with(['message' => __('The document must be a file of type: ', ['files' => 'doc, docx, pdf.'])]);
        }
        $document = new StudentPracticeDocuments();
        $document->user_id = Auth::user()->id;
        $document->document_id = $document_id;
        $document->lang = App::getLocale();
        $document->discipline_id = $discipline_id;
        $document->saveUserDocument($request->all());
        $document->save();

        return redirect()->back();
    }

    public function uploadFile(Request $request, $discipline_id)
    {
        if(!$request->input('link', null) && !$request->file('document', null))
        {
            return redirect()->back()->withErrors(['error' => __('The document must be a file of type: ', ['files' => 'doc, docx, pdf, xls, xlsx, ppt, pptx'])]);
        }

        $validator = Validator::make($request->all(), [
            'document' => 'nullable|file|mimes:doc,docx,pdf,xls,xlsx,ppt,pptx',
            'link'     => 'nullable|url'
        ]);

        if($request->hasFile('document'))
        {
            $fileExt =  $request->file('document')->getClientOriginalExtension();

            if(!$fileExt)
            {
                return redirect()->back()->withErrors(['error' => __('Invalid file name. No extension found.')]);
            }
        }

        if ($validator->fails()){
            if($request->input('link', null))
            {
                return redirect()->back()->withErrors(__('Invalid URL format'));
            }
            else
            {
                return redirect()->back()->withErrors(['error' => __('The document must be a file of type: ', ['files' => 'doc, docx, pdf, xls, xlsx, ppt, pptx'])]);
            }

        }

        $discipline = Discipline::where('id', $discipline_id)->first();

        if(!$discipline)
        {
            abort(404);
        }

        if($discipline->is_practice)
        {
            $file = new StudentPracticeFiles();
            $file->created_at = DB::raw('NOW()');
        }
        else
        {
            $file = new App\Models\StudentDisciplineFile();
        }

        if($request->input('link', null))
        {
            $file->type = 'link';
            $file->link = trim($request->input('link'));
        }
        else
        {
            $file->type = 'file';
            $file->saveFile($request->all());
        }

        $file->user_id = Auth::user()->id;
        $file->discipline_id = $discipline_id;
        $file->save();

        return redirect()->back();
    }

    public function removeFile($discipline_id, $file_id)
    {
        $document = StudentPracticeDocuments::where('discipline_id', $discipline_id)
            ->where('user_id', Auth::user()->id)
            ->where('id', $file_id)
            ->first();

        if (!empty($document)) {
            $document->removeFile();
        }

        return redirect()->back()->with(['message' => __('The file was successfully removed.')]);
    }

    public function removeStudentDisciplineFile($disciplineId, $fileId)
    {
        $discipline = Discipline::where('id', $disciplineId)->first();

        if(!$discipline)
        {
            abort(404);
        }

        if($discipline->is_practice)
        {
            $file = StudentPracticeFiles
                ::where('user_id', App\Services\Auth::user()->id)
                ->where('id', $fileId)
                ->where(DB::raw('TIMESTAMPDIFF(MONTH , created_at, NOW())'), '<=', 5)
                ->first();
        }
        else
        {
            $file = App\Models\StudentDisciplineFile
                ::where('user_id', App\Services\Auth::user()->id)
                ->where('id', $fileId)
                ->where(DB::raw('TIMESTAMPDIFF(MONTH , created_at, NOW())'), '<=', 5)
                ->first();
        }

        if($file)
        {
            $file->delete();
        }

        return redirect()->back()->with(['message' => __('The file was successfully removed.')]);
    }

}
