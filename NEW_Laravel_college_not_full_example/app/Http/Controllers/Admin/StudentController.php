<?php

namespace App\Http\Controllers\Admin;

use App\Services\{
    Auth, DocxHelper, PhpOfficeHelper, SearchCache, Service1C, StudentRating, GenerateStudentContract
};
use App\{AdminStudentComment,
    BcApplications,
    City,
    DisciplineSubmodule,
    NotificationTemplate,
    Order,
    ProfileDoc,
    Profiles,
    QuizQuestion,
    QuizResult,
    QuizeResultKge,
    Region,
    Speciality,
    StudentDiscipline,
    StudentSubmodule,
    StudyGroup,
    Submodule,
    University,
    User,
    Country,
    EntDisciplineList,
    Nationality,
    Trend,
    Language,
    DiscountStudent,
    OrderUser
    };
use App\Models\{
    NobdAcademicLeave,
    NobdAcademicMobility,
    NobdCauseStayYear,
    NobdCountry,
    NobdDisabilityGroup,
    NobdEmploymentOpportunity,
    NobdEvents,
    NobdExchangeSpecialty,
    NobdFormDiplom,
    NobdLanguage,
    NobdPaymentType,
    NobdReasonDisposal,
    NobdReward,
    NobdTrainedQuota,
    NobdTypeDirection,
    NobdTypeEvent,
    NobdTypeViolation,
    NobdStudyExchange,
    NobdUser,
    NobdUserPc
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{File, Log, Mail, Response, Validator};
use App\Validators\{AdminEditProfileChangeEntValidator,AdminEditProfileChangeKtValidator,AdminStudentAjaxGetUserBalanceValidator};
use App\ProfileDocsType;
use DB;
use App\Http\Controllers\Student\DocsController;


class StudentController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        return view('admin.pages.students.list');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAjax(Request $request)
    {
        $searchData = User::getStudentListForAdmin(
            $request->input('search')['value'],
            $request->input('start'),
            $request->input('length'),
            $request->input('order')[0]['column'] ?? 0,
            $request->input('order')[0]['dir'] ?? 'asc'
        );

        return Response::json([
            'draw'            => intval( $request->input('draw') ),
            'recordsTotal'    => $searchData['recordsTotal'],
            'recordsFiltered' => $searchData['recordsFiltered'],
            'data'            => $searchData['data']
        ]);
    }

    public function disciplineMigration(Request $request)
    {
        $inputs = $request->all();

        $discipline = StudentDiscipline
            ::where('student_id', $inputs['studentId'])
            ->where('discipline_id', $inputs['disciplineId'])
            ->first();

        if (!isset($discipline->id)) {
            $discipline = new StudentDiscipline;
            $discipline->discipline_id = $inputs['disciplineId'];
            $discipline->student_id = $inputs['studentId'];
        }

        $discipline->final_result = $inputs['points'];
        $discipline->final_result_points = StudentRating::getFinalResultPoints($inputs['points']);
        $discipline->final_result_letter = StudentRating::getLetter($inputs['points']);
        $discipline->final_result_gpa = StudentRating::getDisciplineGpa($inputs['points'], $inputs['ects']);
        $discipline->migrated = 1;

        if ($inputs['payReq'] == true) {
            $discipline->payed = 0;
        } else {
            $discipline->payed = 1;
        }

        $discipline->save();

        return Response::json([
            'success' => true,
            'value' => $discipline->final_result,
            'points' => $discipline->final_result_points,
            'letter' => $discipline->final_result_letter,
            'gpi' => $discipline->final_result_gpa
        ]);
    }

    public function submoduleDisciplineMigration(Request $request)
    {
        $inputs = $request->all();

        $submoduleId = $inputs['submoduleId'];
        $secondSubmoduleId = Submodule::getDependentId($submoduleId);

        /** @var User $user */
        $user = User::where('id', $inputs['userId'])->first();

        if (empty($user)) {
            return Response::json([
                'success' => false,
                'error' => 'Ошибка. Не найден студент.'
            ]);
        }

        $firstLevelDisciplineId = DisciplineSubmodule::getDisciplineIdByLanguageLevel($submoduleId, $inputs['languageLevel']);
        $secondLevelDisciplineId = DisciplineSubmodule::getSecondLanguageDisciplineId($secondSubmoduleId, $inputs['languageLevel']);

        if (empty($firstLevelDisciplineId) || empty($secondLevelDisciplineId)) {
            return Response::json([
                'success' => false,
                'error' => 'Ошибка. Не найдена дисциплина.'
            ]);
        }

        // First Level Discipline
        $studentDiscipline = StudentDiscipline::add($user->id, $firstLevelDisciplineId, $submoduleId, $user->studentProfile->education_speciality_id);
        $studentDiscipline->final_result = $inputs['points'];
        $studentDiscipline->final_result_points = StudentRating::getFinalResultPoints($inputs['points']);
        $studentDiscipline->final_result_letter = StudentRating::getLetter($inputs['points']);
        $studentDiscipline->final_result_gpa = StudentRating::getDisciplineGpa($inputs['points'], $inputs['ects']);
        $studentDiscipline->migrated = 1;
        $studentDiscipline->payed = ($inputs['payReq']) ? 0 : 1;
        $studentDiscipline->save();

        // Delete student-submodule relation
        StudentSubmodule::deleteRelation($user->id, $submoduleId);

        // Second Level Discipline
        StudentDiscipline::add($user->id, $secondLevelDisciplineId, $secondSubmoduleId, $user->studentProfile->education_speciality_id);

        // Delete student-submodule relation
        StudentSubmodule::deleteRelation($user->id, $secondSubmoduleId);

        return Response::json([
            'success' => true
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $student = User::getStudentForAdmin($id);

        if (!$student || empty($student->studentProfile) ) {
            abort(404);
        }

        $equalSpecialities = $student->studentProfile->equalSpecialities();

        $equalSpecialitiesModels = [];

        if ($equalSpecialities) {
            $equalSpecialitiesModels = Speciality::whereIn('id', array_keys($equalSpecialities))->get();

            foreach ($equalSpecialitiesModels as $k => $specModel) {
                $equalSpecialitiesModels[$k]->eqDisciplineCount = $equalSpecialities[$specModel->id];
            }

            $equalSpecialitiesModels = $equalSpecialitiesModels->sortByDesc('eqDisciplineCount');
        }

        $originalSpeciality = $student->studentProfile->education_original_speciality_id ?
            Speciality::where('id', $student->studentProfile->education_original_speciality_id)->first() :
            null;

        $orderList = Order::get();
        $regions = Region::orderBy('id')->orderBy('name')->get();
        $cities = City::where('hidden', false)->orderBy('id')->orderBy('name')->get();
        $notificationTemplates = NotificationTemplate::getListForAdmin($student);

        $sCodeChar = Speciality::CODE_CHAR_BACHELOR;
        if ($student->mgApplication) {
            $sCodeChar = Speciality::CODE_CHAR_MASTER;
        }

        $yearForSpeciality = isset($student->studentProfile->speciality) ?
            $student->studentProfile->speciality->year :
            date('Y', strtotime($student->created_at));

        $oSpeciality = Speciality
            ::where('code_char', $sCodeChar)
            ->where('year', $yearForSpeciality)
            ->get();

        $nationalityList = Nationality::get();

        $sCurrentLocale = app()->getLocale();
        $locale = Language::getFieldName('name', $sCurrentLocale, Language::LANGUAGE_EN, Language::LANGUAGE_RU);

        $migratedCredits = 0;
        $payReqMigratedCredits = 0;
        foreach ($student->disciplines as $discipline) {
            // Перезачтена
            if ($discipline->getOriginal()['pivot_migrated']) {
                $migratedType = StudentDiscipline::getMigratedType(
                    $discipline->getOriginal()['pivot_migrated'],
                    $discipline->getOriginal()['pivot_payed'],
                    $discipline->getOriginal()['pivot_payed_credits']
                );

                // Оплачена
                if ($migratedType == StudentDiscipline::MIGRATED_TYPE_FREE) {
                    $migratedCredits += $discipline->ects;
                } else {
                    $payReqMigratedCredits += $discipline->ects;
                }
            }
        }

        // Максимум бесплатно
        $maxCreditsAllowed = Auth::user()->id == 18365 ? 120 : $student->migrationMaxFreeCredits;

        // Максимум с требованием оплаты
        $payReqMaxCreditsAllowed = Auth::user()->id == 18365 ? 60 : $student->migrationMaxNotFreeCredits;
        $submodules = StudentSubmodule::getTopSubmodules($student->id);
        $country = Country::get();
        $entDisciplineList = EntDisciplineList::orderBy('name', 'ASC')->get();
        $studyGroupList = StudyGroup::get();
        $univerList = University::get();
        
        // current discount 
        $approvedDiscounts = DiscountStudent::select([
            'discount_student.id',
            'discount_type_list.name_ru AS name',
            'discount_student.status',
            'discount_type_list.discount',
            //'profiles.user_id',
            DB::raw('DATE_FORMAT(discount_student.created_at, "%d-%m-%Y") as created_at'),
            //'category_id',
            //'discount_type_list.id as type_id',
        ])
            ->leftJoin('discount_type_list', 'discount_type_list.id', '=', 'discount_student.type_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'discount_student.user_id')
            ->where('discount_student.user_id', $id)
            ->where('discount_student.status', '=', DiscountStudent::STATUS_APPROVED)
            ->get();

        $userOrders = OrderUser::getUserOrders($id);

        $aMasterDisciplineKtList = [
            "Иностранный язык",
            "Тест на определение готовности к обучению",
            "Педагогика",
            "Биология",
            "Менеджмент",
            "Основы бухгалтерского учета",
            "Экономическая теория",
            "Теория государства и права",
            "Алгоритмические языки программирования",
            "Психология",
            "Методика преподавания биологии",
            "Профессионально-ориентированный иностранный язык (английский, немецкий, французские языки)",
            "Организация бизнеса",
            "Аудит",
            "Макроэкономика",
            "Ситуативный кейс (комплексный юридический анализ конкретной практической ситуации на основе применения норм законодательства РК и/или международного права)",
            "Высшая математика",
        ];

        $docType = ProfileDocsType::whereNull('hidden')->get();


        //Log::info('pc: ' . var_export($student->nobdUser->pc,true));


        return view('admin.pages.students.edit', [
            'student' => $student,
            'equalSpecialities' => $equalSpecialitiesModels,
            'originalSpeciality' => $originalSpeciality,
            'orderList' => $orderList,
            'country' => $country,
            'regions' => $regions,
            'cities' => $cities,
            'id' => $id,
            'notificationTemplates' => $notificationTemplates,
            'oSpeciality' => $oSpeciality,
            'nationalityList' => $nationalityList,
            'locale' => $locale,
            'disciplineList' => !empty($entDisciplineList) ? $entDisciplineList : null,
            'migratedCredits' => $migratedCredits,
            'maxCreditsAllowed' => $maxCreditsAllowed,
            'payReqMaxCreditsAllowed' => $payReqMaxCreditsAllowed,
            'payReqMigratedCredits' => $payReqMigratedCredits,
            'masterDisciplineKtList' => $aMasterDisciplineKtList,
            'docs' => $student->studentProfile->getDocs(),
            'submodules' => $submodules,
            'studyGroupList' => $studyGroupList,
            'univerList' => $univerList,
            'docType' => $docType,
            'approvedDiscounts' => $approvedDiscounts,
            'userOrders' => $userOrders,

            'academicLeave'           => NobdAcademicLeave::whereNull('deleted_at')->get(),
            'academicMobility'        => NobdAcademicMobility::whereNull('deleted_at')->get(),
            'causeStayYear'           => NobdCauseStayYear::whereNull('deleted_at')->get(),
            'countryList'             => NobdCountry::whereNull('deleted_at')->get(),
            'disabilityGroup'         => NobdDisabilityGroup::whereNull('deleted_at')->get(),
            'employmentOpportunity'   => NobdEmploymentOpportunity::whereNull('deleted_at')->get(),
            'events'                  => NobdEvents::whereNull('deleted_at')->get(),
            'exchangeSpecialty'       => NobdExchangeSpecialty::whereNull('deleted_at')->get(),
            'formDiplom'              => NobdFormDiplom::whereNull('deleted_at')->get(),
            'language'                => NobdLanguage::whereNull('deleted_at')->get(),
            'paymentType'             => NobdPaymentType::whereNull('deleted_at')->get(),
            'reasonDisposal'          => NobdReasonDisposal::whereNull('deleted_at')->get(),
            'reward'                  => NobdReward::whereNull('deleted_at')->get(),
            'trainedQuota'            => NobdTrainedQuota::whereNull('deleted_at')->get(),
            'typeDirection'           => NobdTypeDirection::whereNull('deleted_at')->get(),
            'typeEvent'               => NobdTypeEvent::whereNull('deleted_at')->get(),
            'typeViolation'           => NobdTypeViolation::whereNull('deleted_at')->get(),
            'studyExchange'           => NobdStudyExchange::whereNull('deleted_at')->get(),


        ]);
    }

    public function genTranscript($id, Request $request)
    {
        $sector = $request->input('sector');
        return DocsController::genTranscript($id, $sector);
    }

    /**
     * @param Request $request
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function editPost(Request $request, $id)
    {
        $baseEducation = null;

        $student = User::getStudentForAdmin($id);

        if (!$student) {
            abort(404);
        }

        $edudegree = '';
        if ($request->input('bcApplication')) {
            $city = City::where('name', $request->input('bcApplication.city'))->first();
            if (!$city) {
                $city = new City;
                $city->name = $request->input('bcApplication.city');
                $city->hidden = true;
                $city->save();
            }

            $student->bcApplication->fill($request->input('bcApplication'));
            $student->bcApplication->city_id = $city->id;
            $student->bcApplication->save();
            $applicationType = 'bcApplication';
            $edudegree = 'bachelor';
            $baseEducation = $student->bcApplication->education;
        }

        /*Validate password*/
        if ($request->input('password')) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6|confirmed'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator->errors());
            }
        }

        $aInputs = $request->all();
        if ($request->has('isChangeEnt') && !empty($aInputs['isChangeEnt'])) {
            $obValidator = AdminEditProfileChangeEntValidator::make($request->all());
            if ($obValidator->fails()) {
                return redirect()->back()->withErrors($obValidator->errors());
            }

            // init sum
            $iSumBal = 0;
            $iSumBal += intval($request->input('ent_val_1'));
            $iSumBal += intval($request->input('ent_val_2'));
            $iSumBal += intval($request->input('ent_val_3'));
            $iSumBal += intval($request->input('ent_val_4'));
            $iSumBal += intval($request->input('ent_val_5'));

            $obTrend = Trend::
            select([
                'trends.id',
                'trends.name',
                'trends.education_area_code',
                'specialities.id as specialities_id',
                'specialities.trend_id as trend_id',
            ])->
            where('trends.education_area_code', 'like', '%01%')->
            leftJoin('specialities', 'specialities.trend_id', '=', 'trends.id')->
            where('specialities.id', $student->studentProfile->education_speciality_id)->
            get();

            if (!empty($obTrend) && (count($obTrend) > 0)) {
                // для педагогических специальностей балл 60
                if ($iSumBal < 60) {
                    return redirect()->back()->withErrors(['Не правильно выставлены баллы ЕНТ']);
                }

            } else {
                // для иных специальностей балл 50
                if ($iSumBal < 50) {
                    return redirect()->back()->withErrors(['Не правильно выставлены баллы ЕНТ']);
                }
            }

            // фиксируем пред значения ЕНТ один раз
            if (empty($student->bcApplication->ent_name_1_copy) && empty($student->bcApplication->ent_name_2_copy)) {
                $student->bcApplication->ent_name_1_copy = $student->bcApplication->ent_name_1;
                $student->bcApplication->ent_name_2_copy = $student->bcApplication->ent_name_2;
                $student->bcApplication->ent_name_3_copy = $student->bcApplication->ent_name_3;
                $student->bcApplication->ent_name_4_copy = $student->bcApplication->ent_name_4;
                $student->bcApplication->ent_name_5_copy = $student->bcApplication->ent_name_5;
                $student->bcApplication->ent_val_1_copy = $student->bcApplication->ent_val_1;
                $student->bcApplication->ent_val_2_copy = $student->bcApplication->ent_val_2;
                $student->bcApplication->ent_val_3_copy = $student->bcApplication->ent_val_3;
                $student->bcApplication->ent_val_4_copy = $student->bcApplication->ent_val_4;
                $student->bcApplication->ent_val_5_copy = $student->bcApplication->ent_val_5;
            }

            // фиксируем новые значения ЕНТ
            $student->bcApplication->ent_name_1 = $request->input('ent_name_1');
            $student->bcApplication->ent_name_2 = $request->input('ent_name_2');
            $student->bcApplication->ent_name_3 = $request->input('ent_name_3');
            $student->bcApplication->ent_name_4 = $request->input('ent_name_4');
            $student->bcApplication->ent_name_5 = $request->input('ent_name_5');
            $student->bcApplication->ent_val_1 = intval($request->input('ent_val_1'));
            $student->bcApplication->ent_val_2 = intval($request->input('ent_val_2'));
            $student->bcApplication->ent_val_3 = intval($request->input('ent_val_3'));
            $student->bcApplication->ent_val_4 = intval($request->input('ent_val_4'));
            $student->bcApplication->ent_val_5 = intval($request->input('ent_val_5'));

            $student->bcApplication->ent_total = $iSumBal;
            $student->bcApplication->save();
        }

        if ($request->input('mgApplication')) {
            $city = City::where('name', $request->input('mgApplication.city'))->first();
            if (!$city) {
                $city = new City;
                $city->name = $request->input('mgApplication.city');
                $city->hidden = true;
                $city->save();
            }

            $student->mgApplication->fill($request->input('mgApplication'));
            $student->mgApplication->city_id = $city->id;
            $student->mgApplication->save();
            $applicationType = 'mgApplication';
            $edudegree = 'master';
            $baseEducation = $student->mgApplication->education;
        }

        if ($request->has('isChangeKt') && !empty($aInputs['isChangeKt'])) {
            $obValidator = AdminEditProfileChangeKtValidator::make($request->all());
            if ($obValidator->fails()) {
                return redirect()->back()->withErrors($obValidator->errors());
            }

            // init sum
            $iSumBal = 0;
            $iSumBal += intval($request->input('kt_val_1'));
            $iSumBal += intval($request->input('kt_val_2'));
            $iSumBal += intval($request->input('kt_val_3'));
            $iSumBal += intval($request->input('kt_val_4'));

            // фиксируем новые значения ЕНТ
            $student->mgApplication->kt_name_1 = $request->input('kt_name_1');
            $student->mgApplication->kt_name_2 = $request->input('kt_name_2');
            $student->mgApplication->kt_name_3 = $request->input('kt_name_3');
            $student->mgApplication->kt_name_4 = $request->input('kt_name_4');
            $student->mgApplication->kt_val_1 = intval($request->input('kt_val_1'));
            $student->mgApplication->kt_val_2 = intval($request->input('kt_val_2'));
            $student->mgApplication->kt_val_3 = intval($request->input('kt_val_3'));
            $student->mgApplication->kt_val_4 = intval($request->input('kt_val_4'));

            $student->mgApplication->kt_total = $iSumBal;
            $student->mgApplication->save();
        }

        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_FRONT_ID,
            $request->input($applicationType . '.front_id_photo'),
            $request->input($applicationType . '.delivered.front_id_photo'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_BACK_ID,
            $request->input($applicationType . '.back_id_photo'),
            $request->input($applicationType . '.delivered.back_id_photo'));
        /*$student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_RESIDENCE_REGISTRATION,
            $request->input($applicationType . '.residence_registration_status'));*/
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_MILITARY,
            $request->input($applicationType . '.military_status'),
            $request->input($applicationType . '.delivered.military_status'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_R086,
            $request->input($applicationType . '.r086_status'),
            $request->input($applicationType . '.delivered.r086_status'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_R086_BACK,
            $request->input($applicationType . '.r086_status_back'),
            $request->input($applicationType . '.delivered.r086_status_back'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_R063,
            $request->input($applicationType . '.r063_status'),
            $request->input($applicationType . '.delivered.r063_status'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_DIPLOMA,
            $request->input($applicationType . '.diploma_photo'),
            $request->input($applicationType . '.delivered.diploma_photo'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_ATTEDUCATION,
            $request->input($applicationType . '.atteducation_status'),
            $request->input($applicationType . '.delivered.atteducation_status'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_ATTEDUCATION_BACK,
            $request->input($applicationType . '.atteducation_status_back'),
            $request->input($applicationType . '.delivered.atteducation_status_back'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_NOSTRIFICATION,
            $request->input($applicationType . '.nostrification_status'),
            $request->input($applicationType . '.delivered.nostrification_status'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_KT_CERTIFICATE,
            $request->input($applicationType . '.kt_certificate'),
            $request->input($applicationType . '.delivered.kt_certificate'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_EDUCATION_STATEMENT,
            $request->input($applicationType . '.education_statement'),
            $request->input($applicationType . '.delivered.education_statement'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_CON_CONFIRM,
            $request->input($applicationType . '.con_confirm'),
            $request->input($applicationType . '.delivered.con_confirm'));
        $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_ENT_CERTIFICATE,
            $request->input($applicationType . '.ent_certificate'),
            $request->input($applicationType . '.delivered.ent_certificate'));
        $student->studentProfile->updateStatusDocContracts(
            $request->input($applicationType . '.education_contracts'),
            $request->input($applicationType . '.delivered.education_contracts'));

        if ($applicationType == 'mgApplication') {
            $student->studentProfile->updateStatusDoc(ProfileDoc::TYPE_WORK_BOOK,
                $request->input($applicationType . '.work_book_status'),
                $request->input($applicationType . '.delivered.work_book_status'));
        }

        if ($request->input('docs_type_success') == 'true') {
            $student->studentProfile->docs_status = Profiles::DOCS_STATUS_ACCEPT;
        }

        if (
            $request->input($applicationType . '.front_id_photo') == 'disallow' ||
            $request->input($applicationType . '.back_id_photo') == 'disallow' ||
            $request->input($applicationType . '.military_status') == 'disallow' ||
            $request->input($applicationType . '.r086_status') == 'disallow' ||
            $request->input($applicationType . '.r063_status') == 'disallow' ||
            $request->input($applicationType . '.diploma_photo') == 'disallow' ||
            $request->input($applicationType . '.atteducation_status') == 'disallow' ||
            $request->input($applicationType . '.atteducation_status_back') == 'disallow' ||
            $request->input($applicationType . '.nostrification_status') == 'disallow' ||
            $request->input($applicationType . '.kt_certificate') == 'disallow' ||
            $request->input($applicationType . '.education_contract') == 'disallow' ||
            $request->input($applicationType . '.education_statement') == 'disallow'
        ) {
            $student->studentProfile->docs_status = Profiles::DOCS_STATUS_REJECT;
        }

        $student->admission_year = $request->input('admission_year');
        $oldSpecialityId = $student->studentProfile->education_speciality_id;

        if ($request->input('change_speciality_id') &&
            !$student->studentProfile->education_original_speciality_id) {
            $student->studentProfile->education_original_speciality_id = $student->studentProfile->education_speciality_id;
            $student->studentProfile->education_speciality_id = $request->input('change_speciality_id');
        }

        if (!$request->input('change_speciality_id') && $student->studentProfile->education_original_speciality_id) {
            $student->studentProfile->education_speciality_id = $student->studentProfile->education_original_speciality_id;
            $student->studentProfile->education_original_speciality_id = null;
        }

        if ($request->input('change_speciality_id') && $student->studentProfile->education_original_speciality_id) {
            $student->studentProfile->education_speciality_id = $request->input('change_speciality_id');
        }

        // access change mobile phone
        if( !empty($request->input('mobile')) && ( $student->studentProfile->mobile != $request->input('mobile') ) )
        {
            $iProfilesCount = Profiles::
            leftJoin('users', 'users.id', '=', 'profiles.user_id')->
            where('mobile','like', '%' . substr( str_replace(['+7','(',')','-',' '],'',$request->input('mobile')),1) . '%')->
            where('user_id', '!=', $student->id)->
            whereNull('users.deleted_at')->
            count();

            if( $iProfilesCount > 0 )
            {
                return redirect()->back()->withErrors('Ошибка! Такой номер уже есть в системе');
            }
        }

        $student->studentProfile->fio = $request->input('fio') ?? $student->studentProfile->fio;
        $student->studentProfile->bdate = $request->input('bdate') ?? $student->studentProfile->bdate;
        $student->studentProfile->iin = $request->input('iin') ?? $student->studentProfile->iin;
        $student->studentProfile->mobile = $request->input('mobile') ?? $student->studentProfile->mobile;
        $student->studentProfile->docseries = $request->input('docseries') ?? $student->studentProfile->docseries;
        $student->studentProfile->docnumber = $request->input('docnumber') ?? $student->studentProfile->docnumber;
        $student->studentProfile->issuing = $request->input('issuingData') ?? $student->studentProfile->issuing;
        $student->studentProfile->issuedate = $request->input('issuedate') ?? $student->studentProfile->issuedate;
        $student->studentProfile->expire_date = $request->input('expire_date') ?? $student->studentProfile->expire_date;
        $student->studentProfile->alien = !empty($request->input('alien')) ? 1 : 0;
        $student->studentProfile->education_lang = $request->input('education_lang') ?? $student->studentProfile->education_lang;
        $student->studentProfile->education_study_form = $request->input('education_study_form') ?? $student->studentProfile->education_study_form;
        $student->studentProfile->sex = $request->input('sex') ?? $student->studentProfile->sex;
        $student->studentProfile->education_speciality_id = $request->input('education_speciality_id') ?? $student->studentProfile->education_speciality_id;
        $student->studentProfile->nationality_id = $request->input('nationality_id') ?? $student->studentProfile->nationality_id;
        $student->studentProfile->category = $request->input('category') ?? $student->studentProfile->category;
        $student->studentProfile->course = $request->input('course') ?? $student->studentProfile->course;
        $student->studentProfile->workplace = $request->input('workplace') ?? $student->studentProfile->workplace;
        $student->studentProfile->semester_credits_limit = $request->input('semester_credits_limit') ?? null;
        $student->studentProfile->study_group_id = $request->input('study_group_id', null);

        if($student->studentProfile->category == Profiles::CATEGORY_TRANSFER || $student->studentProfile->category == Profiles::CATEGORY_TRANSIT)
        {
            $student->studentProfile->transfer_university_id = $request->input('transfer_university_id', null);
        }
        else
        {
            $student->studentProfile->transfer_university_id = null;
        }

        //Log::info($oldSpecialityId . ' ' . $student->studentProfile->education_speciality_id . ' ' . $student->hasPayedDisciplines());

        if($oldSpecialityId != $student->studentProfile->education_speciality_id && $student->hasPayedDisciplines())
        {
            return redirect()->back()->withErrors('Невозможно сменить специальность, так как есть купленые дисциплины');
        }


        //Log::info('nobdUser: ' . var_export($request->input('nobdUser'),true));


        // NoBDUser
        $oNobdUser = NobdUser::
        where('user_id',$student->id)->
        whereNull('deleted_at')->
        first();
        if( empty($oNobdUser) )
        {
            $oNobdUser = new NobdUser();
        }
        $oNobdUser->user_id = $student->id;
        $oNobdUser->fill($request->input('nobdUser'));
        $oNobdUser->syncFields($request->input('nobdUser'));
        $oNobdUser->updateNobdUserPc($request->input('nobdUser'));
        $oNobdUser->removeNobdUserPc($request->input('removeNobdUserPC'));
        $oNobdUser->save();



        $changeCheckLevel = false;
        $inputCheckLevel = $request->input('check_level');
        if ($inputCheckLevel) {
            if ($inputCheckLevel != $student->studentProfile->check_level) {
                $student->studentProfile->check_level = $inputCheckLevel;
                $changeCheckLevel = true;
            }
        }

        /*Change password*/
        if ($request->input('passwordData')) {
            $student->password = bcrypt($request->input('passwordData'));

            if ($student->keycloak == true) {
                $student->keycloak = false;
            }

            $student->save();
            Log::warning('Change user password.', ['admin_user_id' => Auth::user()->id, 'user_id' => $student->id]);
        }

        $student->save();
        $student->studentProfile->save();
        $student->studentProfile->updateDisciplines();

        // Update search cache
        User::updateSearchCache($student, __($baseEducation ? $baseEducation . '_origin' : 'нет'), __($edudegree ? $edudegree . '_origin' : ''));

        $sendTo = $request->input('send_to');

        if (!$sendTo || $sendTo == 'false') {
            return redirect()->route('adminStudentEdit', ['id' => $student->id])->with('flash_message', 'Изменения сохранены!');
        } elseif ($sendTo == 'true') {
            if ($changeCheckLevel) {
                if ($inputCheckLevel == Profiles::CHECK_LEVEL_OR_CABINET) {
                    return redirect()->route('adminInspectionMatriculantstList');
                }

                if ($inputCheckLevel == Profiles::CHECK_LEVEL_INSPECTION) {
                    return redirect()->route('adminMatriculantstList');
                }
            } else {
                if ($inputCheckLevel == Profiles::CHECK_LEVEL_OR_CABINET) {
                    return redirect()->route('adminMatriculantstList');
                }

                if ($inputCheckLevel == Profiles::CHECK_LEVEL_INSPECTION) {
                    return redirect()->route('adminInspectionMatriculantstList');
                }
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id) {
        $profileTeacher = ProfileTeacher::getTeacherForAdmin($id);
        if(!$profileTeacher)
        {
            abort(404);
        }

        $profileTeacher->delete();

        return redirect()->route('adminTeacherList');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteResult(Request $request, $id) {
        $studentDicscipline = StudentDiscipline
            ::where('discipline_id', $request->input('discipline_id'))
            ->where('student_id', $id)
            ->first();

        if(!$studentDicscipline)
        {
            abort(400);
        }

        QuizResult::where('student_discipline_id', $studentDicscipline->id)->delete();
        $studentDicscipline->final_result = null;
        $studentDicscipline->final_result_points = null;
        $studentDicscipline->final_result_gpa = null;
        $studentDicscipline->final_result_letter = '';
        $studentDicscipline->migrated = 0;
        $studentDicscipline->save();

        return Response::json([]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteResultKge($id)
    {
        QuizeResultKge::where('user_id', $id)->delete();

        return Response::json();
    }

    /**
     * @param $userId
     * @param $disciplineId
     */
    public function showResult($id, $disciplineId)
    {
        if($disciplineId == 'kge')
        {
            $result = QuizeResultKge::where('user_id', $id)->orderBy('id', 'desc')->first();
        }
        else
        {
            $result = QuizResult
                ::where('user_id', $id)
                //->leftJoin('students_disciplines', 'students_disciplines.id', '=', 'quize_result.student_discipline_id')
                //->where('students_disciplines.discipline_id', $disciplineId)
                ->where('discipline_id', $disciplineId)
                ->orderBy('quize_result.id', 'desc')
                ->first();
        }

        if(!$result)
        {
            abort(404);
        }

        $answers = $result->answers;
        $questionList = [];

        foreach ($answers as $answer)
        {
            $questionList[$answer->question_id]['answer_id'][] = $answer->answer_id;
            if(!isset($questionList[$answer->question_id]['question']))
            {
                $question = QuizQuestion
                    ::where('id', $answer->question_id)
                    ->with('answers')
                    ->first();

                if(!$question)
                {
                    unset($questionList[$answer->question_id]);
                }
                else
                {
                    $questionList[$answer->question_id]['question'] = $question;
                }
            }
        }

        return view('admin.pages.students.disciplines.result', compact('questionList', 'id'));
    }

    /**
     * @param $userId
     * @param $disciplineId
     */
    public function showResultExam($id, $disciplineId)
    {

        $result = QuizResult
            ::where('user_id', $id)
            ->where('type', 'exam')
            ->where('blur', 0)
            //->leftJoin('students_disciplines', 'students_disciplines.id', '=', 'quize_result.student_discipline_id')
            //->where('students_disciplines.discipline_id', $disciplineId)
            ->where('discipline_id', $disciplineId)
            ->orderBy('quize_result.id', 'desc')
            ->first();

        if(!$result)
        {
            abort(404);
        }

        $answers = $result->answers;
        $questionList = [];

        foreach ($answers as $answer)
        {
            $questionList[$answer->question_id]['answer_id'][] = $answer->answer_id;
            if(!isset($questionList[$answer->question_id]['question']))
            {
                $question = QuizQuestion
                    ::where('id', $answer->question_id)
                    ->with('answers')
                    ->first();

                if(!$question)
                {
                    unset($questionList[$answer->question_id]);
                }
                else
                {
                    $questionList[$answer->question_id]['question'] = $question;
                }
            }
        }

        return view('admin.pages.students.disciplines.result', compact('questionList', 'id'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeIgnoreDebt(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        if(!$user)
        {
            abort(500, 'user not found');
        }

        $ignoreDebt = $request->input('ignore_debt');
        $user->studentProfile->ignore_debt = $ignoreDebt == 'true' ? true : false;
        $user->studentProfile->save();

        return Response::json();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     *
     */
    public function addComment(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::where('id', $userId)->first();
        $text = $request->input('text');

        if( empty($user->studentProfile) )
        {
            return Response::json();
        }

        AdminStudentComment::insert([
            'author_id' => Auth::user()->id,
            'user_id'   => $userId,
            'check_level'   => $user->studentProfile->scheck_level,
            'text'      => $text,
            'created_at'    => \DB::raw('NOW()')
        ]);

        return Response::json();
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function refreshEnt(Request $request, $id)
    {
        $user = User::where('id', $id)->first();
        $ikt = $request->input('ikt');

        if(!$user || !$ikt)
        {
            abort(404);
        }

        $ent = $user->bcApplication->importEnt($ikt, $user->studentProfile->iin);
        if( !isset($ent->errorCode) || $ent->errorCode != 0 ) $ent = ['userBallList' => []];

        return Response::json($ent);
    }

    /**
     * generate note education document
     * @param request $request
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function generateNoteEducationDocument(Request $request, $id)
    {

        $aConfirmedDocs = [];
        $oStudent = User::getStudentForAdmin($id);
        if( empty( $oStudent->studentProfile )  )
        {
            abort(404);
        }

        $currentLocale = app()->getLocale();
        app()->setLocale('ru');
        $educationForm = __($oStudent->studentProfile->education_study_form);
        $studyLang = __($oStudent->studentProfile->education_lang);
        app()->setLocale($currentLocale);

        $sEducation = __('Diploma/Certificate');
        $sEducationApplication = __('Diploma/Certificate Application');

        $params = [
            '${t_fio}'              => $oStudent->studentProfile->fio,
            '${t_education_form}'   => $educationForm,
            '${t_trend}'            => $oStudent->studentProfile->speciality->trend->name,
            '${t_speciality}'       => $oStudent->studentProfile->speciality->name,
            '${t_study_language}'   => $studyLang,
            '${t_date}'             => date('d.m.Y'),
            '${t_agitator}'         => !empty($oStudent->referral_name) ? $oStudent->referral_name : '',
            '${t_tech_human}'       => \App\Services\Auth::user()->name,
        ];

        // processing education confirmed document note list
        if( $oStudent->studentProfile->front_id_photo && $oStudent->studentProfile->back_id_photo &&
            ( $oStudent->studentProfile->front_id_photo->delivered == true ) &&
            ( $oStudent->studentProfile->back_id_photo->delivered == true ) )
        {
            $aConfirmedDocs[] = __('Identity document (copy)');
        }
        if( $oStudent->studentProfile->diploma_photo &&
            ( $oStudent->studentProfile->diploma_photo->delivered == true ) )
        {
            $sGraduation = __('Documents about graduation') . ': ' . $sEducation;
            if( $oStudent->studentProfile->doc_atteducation && ( $oStudent->studentProfile->doc_atteducation->delivered == true ) &&
                $oStudent->studentProfile->doc_atteducation_back && ( $oStudent->studentProfile->doc_atteducation_back->delivered == true )
            ) {
                $sGraduation .= ($sGraduation != '') ? ', ': '';
                $sGraduation .=  $sEducationApplication;
            }
            $aConfirmedDocs[] = $sGraduation;
        }

        if( $oStudent->bcApplication && $oStudent->studentProfile->doc_ent && ( $oStudent->studentProfile->doc_ent->delivered == true ))
        {
            $aConfirmedDocs[] = __('ENT certificate');
        }
        if( $oStudent->mgApplication && $oStudent->studentProfile->doc_kt && ( $oStudent->studentProfile->doc_kt->delivered == true ))
        {
            $aConfirmedDocs[] = __('KT certificate');
        }

        if( $oStudent->studentProfile->doc_r086 && $oStudent->studentProfile->doc_r086_back &&
            ( $oStudent->studentProfile->doc_r086->delivered == true ) && ( $oStudent->studentProfile->doc_r086_back->delivered == true ) )
        {
            $sMed = __('Reference 086');
            if( $oStudent->studentProfile->doc_r063 && ( $oStudent->studentProfile->doc_r063->delivered == true )) {
                $sMed .= ($sMed != '') ? ', ': '';
                $sMed .= __('Reference 063');
            }
            $aConfirmedDocs[] = $sMed;
        }
        if( $oStudent->studentProfile->doc_military && ( $oStudent->studentProfile->doc_military->delivered == true ))
        {
            $aConfirmedDocs[] = __('Military enlistment office');
        }
        ///////////////////////

        $file1 = DocxHelper::replace(resource_path('docx/education_document_note_template.docx'), $params, 'docx');
        $file = PhpOfficeHelper::addTableForProfileNoteOpisList($file1,$aConfirmedDocs);
        File::delete($file1);

        return Response::download($file, 'Расписка принятых документов.docx')->deleteFileAfterSend(true);

    }

    /**
     * generate opis education document
     * @param request $request
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function generateOpisEducationDocument(Request $request, $id)
    {

        $aConfirmedDocs = [];
        $oStudent = User::getStudentForAdmin($id);
        if( empty( $oStudent->studentProfile )  )
        {
            abort(404);
        }

        $currentLocale = app()->getLocale();
        app()->setLocale('ru');
        $educationForm = __($oStudent->studentProfile->education_study_form);
        $studyLang = __($oStudent->studentProfile->education_lang);
        app()->setLocale($currentLocale);

        $sEducation = __('Diploma/Certificate');
        $sEducationApplication = __('Diploma/Certificate Application');

        $params = [
            '${t_fio}'              => $oStudent->studentProfile->fio,
            '${t_education_form}'   => $educationForm,
            '${t_trend}'            => $oStudent->studentProfile->speciality->trend->name,
            '${t_speciality}'       => $oStudent->studentProfile->speciality->name,
            '${t_study_language}'   => $studyLang,
            '${t_date}'             => date('d.m.Y'),
            '${t_agitator}'         => !empty($oStudent->referral_name) ? $oStudent->referral_name : '',
            '${t_tech_human}'       => \App\Services\Auth::user()->name,
        ];

        // processing education confirmed document note list
        if( $oStudent->studentProfile->front_id_photo && $oStudent->studentProfile->back_id_photo &&
            ( $oStudent->studentProfile->front_id_photo->delivered == true ) &&
            ( $oStudent->studentProfile->back_id_photo->delivered == true ) )
        {
            $aConfirmedDocs[] = __('Identity document (copy)');
        }
        if( $oStudent->studentProfile->diploma_photo &&
            ( $oStudent->studentProfile->diploma_photo->delivered == true ) )
        {
            $sGraduation = __('Documents about graduation') . ': ' . $sEducation;
            if( $oStudent->studentProfile->doc_atteducation && ( $oStudent->studentProfile->doc_atteducation->delivered == true ) &&
                $oStudent->studentProfile->doc_atteducation_back && ( $oStudent->studentProfile->doc_atteducation_back->delivered == true )
            ) {
                $sGraduation .= ($sGraduation != '') ? ', ': '';
                $sGraduation .=  $sEducationApplication;
            }
            $aConfirmedDocs[] = $sGraduation;
        }

        if( $oStudent->bcApplication && $oStudent->studentProfile->doc_ent && ( $oStudent->studentProfile->doc_ent->delivered == true ))
        {
            $aConfirmedDocs[] = __('ENT certificate');
        }
        if( $oStudent->mgApplication && $oStudent->studentProfile->doc_kt && ( $oStudent->studentProfile->doc_kt->delivered == true ))
        {
            $aConfirmedDocs[] = __('KT certificate');
        }

        if( $oStudent->studentProfile->doc_r086 && $oStudent->studentProfile->doc_r086_back &&
            ( $oStudent->studentProfile->doc_r086->delivered == true ) && ( $oStudent->studentProfile->doc_r086_back->delivered == true ) )
        {
            $sMed = __('Reference 086');
            if( $oStudent->studentProfile->doc_r063 && ( $oStudent->studentProfile->doc_r063->delivered == true )) {
                $sMed .= ($sMed != '') ? ', ': '';
                $sMed .= __('Reference 063');
            }
            $aConfirmedDocs[] = $sMed;
        }
        if( $oStudent->studentProfile->doc_military && ( $oStudent->studentProfile->doc_military->delivered == true ))
        {
            $aConfirmedDocs[] = __('Military enlistment office');
        }
        ///////////////////////

        $file1 = DocxHelper::replace(resource_path('docx/education_document_opis_template.docx'), $params, 'docx');
        $file = PhpOfficeHelper::addTableForProfileNoteOpisList($file1,$aConfirmedDocs);
        File::delete($file1);

        return Response::download($file, 'Опись принятых документов.docx')->deleteFileAfterSend(true);

    }

    /**
     * generate title list
     * @param request $request
     * @param int $id
     * @return string
     * @throws \Exception
     */
    public function generateTitleList(Request $request, $id)
    {

        $oStudent = User::getStudentForAdmin($id);
        if( empty( $oStudent->studentProfile )  )
        {
            abort(404);
        }

        $sDateEducation = '';
        $sNameEducation = '';
        $sMilitary = '';
        $sAdress = '';
        $sEducation = '';
        $sNationality = '';
        $sFio = $oStudent->studentProfile->fio;
        $aFio = explode(' ',$sFio);

        if( !empty($oStudent->bcApplication) )
        {
            $sDateEducation = date('d.m.Y',strtotime($oStudent->bcApplication->dateeducation));
            $sNameEducation = $oStudent->bcApplication->nameeducation;
            $sEducation = __($oStudent->bcApplication->education);
            $sAdress = implode(', ', [
                $oStudent->bcApplication->region->name ?? '',
                $oStudent->bcApplication->city->name ?? '',
                $oStudent->bcApplication->street ?? '',
                $oStudent->bcApplication->building_number ?? '',
                $oStudent->bcApplication->apartment_number ?? ''
            ] );
        } elseif( !empty($oStudent->mgApplication) ) {
            $sDateEducation = date('d.m.Y',strtotime($oStudent->mgApplication->dateeducation));
            $sNameEducation = $oStudent->mgApplication->nameeducation;
            $sEducation = __($oStudent->mgApplication->education);
            $sAdress = implode(', ', [
                $oStudent->mgApplication->region->name ?? '',
                $oStudent->mgApplication->city->name ?? '',
                $oStudent->mgApplication->street ?? '',
                $oStudent->mgApplication->building_number ?? '',
                $oStudent->mgApplication->apartment_number ?? ''
            ] );
        }
        if( $oStudent->studentProfile->doc_military && ( $oStudent->studentProfile->doc_military->delivered == true ))
        {
            $sMilitary = __('Yes');
        }

        if( !empty($oStudent->studentProfile->nationality_ru) )
        {
            $sNationality = $oStudent->studentProfile->nationality_ru;
        } elseif( !empty($oStudent->nationalityItem) ){
            $sNationality = $oStudent->nationalityItem->name_ru;
        }

        $params = [
            '${t_date}'             => date('Y'),
            '${t_sername}'          => !empty($aFio[0]) ? $aFio[0] : '',
            '${t_name}'             => !empty($aFio[1]) ? $aFio[1] : '',
            '${t_last_name}'        => !empty($aFio[2]) ? $aFio[2] : '',
            '${t_bdate}'            => date('d.m.Y',strtotime($oStudent->studentProfile->bdate)),
            '${t_nationality}'      => $sNationality,
            '${t_gender}'           => ($oStudent->studentProfile->sex === 1) ? __('male') : __('female'),
            '${t_issuedate}'        => $sNameEducation . ', ' . $sDateEducation,
            '${t_honours}'          => '',
            '${t_grant}'            => '',
            '${t_military}'         => $sMilitary,
            '${t_orphan}'           => '',
            '${t_disability}'       => '',
            '${t_adress}'           => $sAdress,
            '${t_actual_adress}'    => $sAdress,
            '${t_phone}'            => $oStudent->studentProfile->mobile,
            '${t_email}'            => $oStudent->email,
            '${t_specialty}'        => $oStudent->studentProfile->speciality->name,
            '${t_education}'        => $sEducation,
            '${t_education_form}'   => __($oStudent->studentProfile->education_study_form),
            '${t_education_lang}'   => __($oStudent->studentProfile->education_lang),
            '${t_date_doc}'         => date('d.m.Y'),
        ];

        $file = DocxHelper::replace(resource_path('docx/title_list_template.docx'), $params, 'docx');

        return Response::download($file, 'Титульный лист.docx')->deleteFileAfterSend(true);


    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxGetUserBalance(Request $request)
    {

        // validation data
        $obValidator = AdminStudentAjaxGetUserBalanceValidator::make( $request->all() );
        if( $obValidator->fails() )
        {
            return \Response::json([
                'status'  => false,
                'message' => __('Error input data')
            ]);
        }

        $oUser = User
            ::with('studentProfile')
            ->select([
                'id',
                'balance'
            ])
            ->where('id',$request->input('id'))->
            first();

        if( !empty($oUser) )
        {
            $newBalance = Service1C::getBalance($oUser->studentProfile->iin);

            if($newBalance !== false)
            {
                $oUser->balance = $newBalance;
                $oUser->save();
            }

            return \Response::json([
                'status' => true,
                'data'   => $oUser
            ]);
        }

        return \Response::json([
            'status'       => false,
            'message'      => __('Error input data\'')
        ]);

    }

    /**
     * generate education contract
     * @param
     * @return string
     * @throws \Exception
     */
    public function generateEducationContract($id)
    {
        return GenerateStudentContract::generateEducationContract($id);
    }

    /**
     * generate education statement
     * @param
     * @return string
     * @throws \Exception
     */
    public function printEducationStatement($id)
    {
        return GenerateStudentContract::printEducationStatement($id);
    }

    /**
     * Ajax POST set free credits to discipline
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setFreeCredits(Request $request)
    {
        $inputs = $request->all();

        if (!isset($inputs['freeCredits']) || !isset($inputs['studentId']) || !isset($inputs['disciplineId'])) {
            return Response::json(['success' => false]);
        }

        $discipline = StudentDiscipline::getOne($inputs['studentId'], $inputs['disciplineId']);

        if (empty($discipline)) {
            return Response::json(['success' => false]);
        }

        $discipline->free_credits = (int)$inputs['freeCredits'];
        $discipline->save();

        return Response::json(['success' => true]);
    }
}
