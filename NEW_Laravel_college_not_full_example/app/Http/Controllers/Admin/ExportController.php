<?php

namespace App\Http\Controllers\Admin;

use App\Profiles;
use App\{
    Discipline,
    DisciplinesPracticePay,
    OrderUser,
    Speciality,
    StudentDiscipline,
    StudentGroupsSemesters,
    StudyGroup,
    SyllabusTaskCoursePay
};
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpWord\TemplateProcessor;

class ExportController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function exportStudentForm()
    {
        return view('admin.pages.export.students');
    }

    public function exportStudentPost(Request $request)
    {
        ini_set("memory_limit", "512M");

        $year = (int)$request->input('year', 0);

        $limit = 1000;
        $i = 0;
        $rows = $this->getRows($year, 0, $limit-1);
        $resultRows = [];

        while(count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $resultRows[] = $row;
            }

            $i++;
            $rows = $this->getRows($year, ($i * $limit) - 1, $limit);
        }

        return Excel::create( 'Students' . ($year ? ('_' . $year) : ''), function($excel) use ($resultRows, $year) {
            $excel->sheet('List 1', function($sheet) use ($resultRows)
            {
                $sheet->fromArray($resultRows);
            });
        })->download('xlsx');

    }

    public function exportStudentByDate(Request $request)
    {
        ini_set("memory_limit", "512M");

        $date = $request->has('date') && !is_null($request['date']) ? $request['date'] : '2015-01-01';
        $limit = 1000;

        $i = 0;
        $rows = $this->getRows('', 0, $limit-1, $date);
        $resultRows = [];

        while(count($rows) > 0) {

            foreach ($rows as $row) {
                $resultRows[] = $row;
            }
            $i++;
            $rows = $this->getRows('', ($i * $limit) - 1, $limit, $date);
        }

        return Excel::create( 'Students1' . ($date ? ('_' . $date) : ''), function($excel) use ($resultRows) {
            $excel->sheet('List 1', function($sheet) use ($resultRows)
            {
                $sheet->fromArray($resultRows);
            });
        })->download('xlsx');

    }

    public function getRows($year='', $offset=0, $limit, $date='')
    {
        if($date) {
            $yearReq = "users.created_at >= '2015-01-01' and users.created_at < '". $date ."' and ";
        } else {
            $yearReq = $year ? "users.created_at >= '" . $year . "-01-01' and users.created_at < '" . ($year+1) . "-01-01' and " : "users.created_at >= '2015-01-01' and users.created_at < '" . (date('Y', time()) + 1) . "-01-01' and ";
        }
        //$fp = fopen($fileName, 'w+');

        $userList = DB::select(DB::raw("
        select
       users.id as id,
       profiles.fio as 'fio',
       s.name as 'speciality',
       se.name as 'elective_speciality',
       users.email as 'email',
       profiles.mobile as 'phone',
       profiles.education_status as 'status',
       DATE_FORMAT(profiles.bdate, '%d.%m.%Y') as 'bdate',
       YEAR(now()) - YEAR(profiles.bdate) - (DATE_FORMAT(now(), '%m%d') < DATE_FORMAT(profiles.bdate, '%m%d')) as 'age',
       case profiles.education_study_form when 'fulltime' then 'очная' when 'night' then 'вечерняя' when 'online' then 'удаленная' when 'evening' then 'вечерняя' when 'extramural' then 'заочная' end as 'education_form',
       profiles.iin as 'iin',
       case (case when bca.education is not null then bca.education else mga.education end) when 'higher' then 'высшее' when 'bachelor' then 'высшее' when 'high_school' then 'среднее' when 'vocational_education' then 'средне-специальное' end as 'base_education',
       case when bca.id is not null then 'бакалавр' else 'магистр' end as 'degree',
       profiles.course as course,
       case profiles.category when 'matriculant' then 'абитуриент' when 'standart' then 'стандарт' when 'standart_recount' then 'стандарт(перезачет)' when 'transit' then 'транзит' when 'trajectory_change' then 'смена траектории'  when 'retake_ent' then 'пересдача ЕНТ' when 'transfer' then 'переводник' end as 'category',
       sg.name as 'group',
       users.balance as 'balance',
       profiles.education_lang as 'education_language',
       case profiles.sex when 0 then 'женский' when 1 then 'мужской' end as 'gender',
       case when bca.id is not null then CONCAT(IFNULL(c1.name, ''), ', ', IFNULL(bca.street,''), ' ', IFNULL(bca.building_number, ''), ' ', IFNULL(bca.apartment_number, '')) else CONCAT( IFNULL(c2.name,''), ', ', IFNULL(mga.street, ''), ' ', IFNULL(mga.building_number,''), ' ', IFNULL(mga.apartment_number, '')) end as 'address',
       concat(profiles.docseries, ' ', profiles.docnumber, ' ', profiles.issuing, profiles.issuedate) as 'document',
       case when bca.id is not null then bca.nameeducation else mga.nameeducation end as 'education_name',
       case when bca.id is not null then bca.sereducation else mga.sereducation end as 'e_series',
       case when bca.id is not null then bca.numeducation else mga.numeducation end as 'e_number',
       case when bca.id is not null then DATE_FORMAT(bca.dateeducation, '%d.%m.%Y') else DATE_FORMAT(mga.dateeducation, '%d.%m.%Y') end as 'e_date',
       case when bca.id is not null then concat('ENT Total: ', bca.ent_total, ', ', bca.ent_name_1, ': ', bca.ent_val_1, ', ', bca.ent_name_2, ': ', bca.ent_val_2, ', ', bca.ent_name_3, ': ', bca.ent_val_3, ', ', bca.ent_name_4, ': ', bca.ent_val_4, ', ', bca.ent_name_5, ': ', bca.ent_val_5) else concat('IKT Total: ', mga.kt_total, ', ', mga.kt_name_1, ': ', mga.kt_val_1, ', ', mga.kt_name_2, ': ', mga.kt_val_2, ', ', mga.kt_name_3, ': ', mga.kt_val_3, ', ', mga.kt_name_4, ': ', mga.kt_val_4) end as 'ent_ikt',
       nl.name_ru as 'nationality',
       case when bca.id is not null then cl1.name else cl2.name end as 'citizenship',
       case profiles.alien when 1 then 'да' when 0 then 'нет' end as 'alien',
       DATE_FORMAT(users.created_at, '%d.%m.%Y') as 'created_at',
       users.referral_name as 'agitator',
       users.referral_source as 'source'

from profiles

         left join specialities s on profiles.education_speciality_id = s.id
         left join specialities se on profiles.elective_speciality_id = se.id
         left join users on users.id = profiles.user_id
         left join bc_applications bca on bca.user_id = users.id
         left join country_list cl1 on cl1.id = bca.citizenship_id
         left join country_list cl11 on cl11.id = bca.country_id
         left join cities c1 on c1.id = bca.city_id
         left join regions r1 on r1.id = bca.region_id
         left join nationality_list nl on nl.id = profiles.nationality_id
         left join mg_applications mga on mga.user_id = users.id
         left join country_list cl2 on cl2.id = mga.citizenship_id
         left join country_list cl22 on cl22.id = mga.country_id
         left join cities c2 on c2.id = mga.city_id
         left join regions r2 on r2.id = mga.region_id
         left join study_groups sg on sg.id = profiles.study_group_id
where $yearReq
      users.deleted_at is null
      and profiles.registration_step = 'finish'
      and (bca.id is not null || mga.id is not null)
limit $limit
offset $offset
        "));

        $allRows = [];

        foreach ($userList as $user)
        {
            $data = [
                'ID' => $user->id,
                'ФИО' => $user->fio,
                'Специальность' => $user->speciality,
                'Элективная специальность' => $user->elective_speciality,
                'Статус' => $date ? __(OrderUser::getUserOrdersStatusByDate($user->id, $date)) : __($user->status),
                'Email' => $user->email,
                'Телефон' => $user->phone,
                'Дата рождения' => $user->bdate,
                'Возраст' => $user->age,
                'Форма обучения' => $user->education_form,
                'ИИН' => $user->iin,
                'Баз. обр.' => $user->base_education,
                'Степень' => $user->degree,
                'Курс' => $user->course,
                'Категория' => $user->category,
                'Группа' => $user->group,
                'Баланс' => $user->balance,
                'Язык обучения' => $user->education_language,
                'Пол' => $user->gender,
                'Адрес' => $user->address,
                'Удостоверение личности' => $user->document,
                'Наименование Учебного Заведения' => $user->education_name,
                'Серия ДО' => $user->e_series,
                'Номер ДО' => $user->e_number,
                'Дата выдачи ДО' => $user->e_date,
                'Данные ЕНТ/ИКТ' => $user->ent_ikt,
                'Национальность' => $user->nationality,
                'Гражданство' => $user->citizenship,
                'Иностранец' => $user->alien,
                'Дата приема' => $user->created_at,
                'Агитатор' => $user->agitator ? $user->agitator : $user->source
            ];

            $allRows[] = $data;
        }

        return $allRows;
    }


    

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function exportExamResult()
    {
        $groupList = StudyGroup::get();

        return view('admin.pages.export.exam_results', compact('groupList'));
    }

    public function exportExamResultPost(Request $request)
    {
        ini_set("memory_limit", "1000M");
        ini_set('max_execution_time', 0);

        $year = (int)$request->input('year', 0);
        $course = (int)$request->input('course', 0);
        $fileName = public_path('/export/Students_' . $year . '_course_' . $course . '.csv');

        $reportFile = fopen($fileName, 'w');

        if(!$year || !$course)
        {
            abort(404);
        }

        $limit = 10000;
        $i = 0;
        $rows = $this->getExamResultRows($year, $course, 0, $limit-1);
        $resultRows = [];

        fputcsv($reportFile, [
            'ID',
            'ФИО',
            'Статус',
            'Категория',
            'Степень',
            'Форма обучения',
            'Базовое образование',
            'Курс',
            'Телефон',
            'ИИН',
            'Группа',
            'Специальность',
            'Дисциплина',
            'ID дисциплины',
            'Семестр дисциплины',
            'Семестр, в котором куплена дисциплина',
            'Перезачет',
            'Финальный результат',
            'Гражданство',
            'СРО',
            'Т1',
            'Т2'
        ]);

        while(count($rows) > 0)
        {
            foreach ($rows as $row)
            {
                $reportRow = Collection::make($row);
                $reportRow = $reportRow->values()->toArray();
                fputcsv($reportFile, $reportRow);
            }

            $i++;
            $rows = $this->getExamResultRows($year, $course, ($i * $limit) - 1, $limit);
        }

        fclose($reportFile);

        return response()->download($fileName)->deleteFileAfterSend();
    }

    public function getExamResultRows($year='', $course, $offset=0, $limit)
    {
        $yearReq = $year ? "users.created_at >= '" . $year . "-01-01' and users.created_at < '" . ($year+1) . "-01-01' and " : "users.created_at >= '2015-01-01' and users.created_at < '" . (date('Y', time()) + 1) . "-01-01' and ";
        $courseReq = $course ? ("profiles.course = " . $course . ' and ') : '';

        $userList = DB::select(DB::raw("
        select
    profiles.user_id as 'id',
    profiles.fio as 'fio',
    case profiles.education_status when 'matriculant' then 'абитуриент' when 'student' then 'студент' when 'send_down' then 'отчислен' when 'academic_leave' then 'академ. отпуск' when 'pregraduate' then 'преддиплом' when 'graduate' then 'выпускник' when 'temp_suspended' then 'временно отчислен' end as 'status',
    case profiles.category when 'matriculant' then 'абитуриент' when 'standart' then 'стандарт' when 'standart_recount' then 'стандарт(перезачет)' when 'transit' then 'транзит' when 'trajectory_change' then 'смена траектории'  when 'retake_ent' then 'пересдача ЕНТ' when 'transfer' then 'переводник' end as 'category',
    case when bca.id is not null then 'бакалавр' else 'магистр' end as 'degree',
    case profiles.education_study_form when 'fulltime' then 'очная' when 'night' then 'вечерняя' when 'online' then 'удаленная' when 'evening' then 'вечерняя' when 'extramural' then 'заочная' end as 'study_form',
    case (case when bca.education is not null then bca.education else mga.education end) when 'higher' then 'высшее' when 'bachelor' then 'высшее' when 'high_school' then 'среднее' when 'vocational_education' then 'средне-специальное' end as 'base_education',
    profiles.course as 'course',
    profiles.mobile as 'mobile',
    profiles.iin as 'iin',
    study_groups.name as 'group',
    s.name as 'speciality',
    d.name as 'discipline',
    d.id as 'id_discipline',
    spd.semester as 'semester',
    sd.at_semester as 'at_semester',
    sd.migrated as 'migrated',
    sd.final_result as 'final_result',
    case when bca.id is not null then cl1.name else cl2.name end as 'citizenship',
    sd.task_result as 'sro',
    sd.test1_result as 't1',
    sd.test_result as 't2'
from students_disciplines as sd
        left join disciplines d on d.id = sd.discipline_id
        left join profiles on profiles.user_id = sd.student_id
        left join specialities s on profiles.education_speciality_id = s.id
        left join speciality_discipline spd on spd.speciality_id = profiles.education_speciality_id and spd.discipline_id = sd.discipline_id
        left join study_groups on study_groups.id = profiles.study_group_id
        left join users on users.id = profiles.user_id
        left join bc_applications bca on bca.user_id = users.id
        left join country_list cl1 on cl1.id = bca.citizenship_id
        left join mg_applications mga on mga.user_id = users.id
        left join country_list cl2 on cl2.id = mga.citizenship_id
where $yearReq
      $courseReq
      users.deleted_at is null
      and profiles.registration_step = 'finish'
      and (bca.id is not null || mga.id is not null)
limit $limit
offset $offset
        "));

        return $userList;
    }

    public function examSheetChoose()
    {
        $disciplines = Discipline::getArrayForSelect();
        $semesters = $studentIds = StudentGroupsSemesters::getSemesters();

        return view('admin.pages.export.exam_sheet_choose', compact('disciplines', 'semesters'));
    }

    public function exportExamSheet(Request $request)
    {
        if (empty($request->input('discipline_id'))) {
            $this->flash_danger('Не выбрна дисциплина');
            return redirect()->route('adminExportExamSheets');
        }

        if (empty($request->input('group_id'))) {
            $this->flash_danger('Не выбрна группа');
            return redirect()->route('adminExportExamSheets');
        }

        $discipline = Discipline::getById($request->input('discipline_id'));

        if (empty($discipline)) {
            $this->flash_danger('Дисциплина не найдена');
            return redirect()->route('adminExportExamSheets');
        }

        $studentIds = StudentGroupsSemesters::getUserIds($request->input('group_id'), $request->input('semester'));
        $studentDisciplines = StudentDiscipline::getByStudentIdsAndDisciplineId($studentIds, $discipline->id);
        $resultCounts = StudentDiscipline::getResultsCounts($studentDisciplines);

        $templateProcessor = new TemplateProcessor(storage_path('export_exam_sheet.docx'));

        $templateProcessor->setValue('discipline_name', $discipline->name . " ($discipline->ects кредитов)");
        $templateProcessor->setValue('a_count', $resultCounts['a']);
        $templateProcessor->setValue('b_count', $resultCounts['b']);
        $templateProcessor->setValue('cd_count', $resultCounts['cd']);
        $templateProcessor->setValue('f_count', $resultCounts['f']);

        $templateProcessor->cloneRowAndSetValues('user_n', StudentDiscipline::getArrayForExamSheet($studentDisciplines));

        if (!is_dir(storage_path('export/exam_sheets'))) {
            mkdir(storage_path('export/exam_sheets'));
        }

        $fileName = 'exam_sheet_' . $discipline->id . '_' . $request->input('group_id') . '.docx';
        $filePath = storage_path('export/exam_sheets/' . $fileName);
        $templateProcessor->saveAs($filePath);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        $this->flash_danger('Ошибка создания файла');
        return redirect()->route('adminExportExamSheets');
    }

    public function exportPractice()
    {
        return view('admin.pages.export.practice');
    }

    public function exportPracticePost()
    {
        $studyForms = [
            Profiles::EDUCATION_STUDY_FORM_FULLTIME     => 'Очная',
            Profiles::EDUCATION_STUDY_FORM_ONLINE       => 'Онлайн',
            Profiles::EDUCATION_STUDY_FORM_EVENING      => 'Вечерняя',
            Profiles::EDUCATION_STUDY_FORM_EXTRAMURAL   => 'Заочная',
        ];

        $disciplinePracticePay = DisciplinesPracticePay::all();
        $resultRows = [];

        foreach ($disciplinePracticePay as $practicePay){
            $student = $practicePay->user;
            $study_form = $student->studentProfile->education_study_form;
            $group = $student->studentProfile->team;
            $lang = $student->studentProfile->education_lang;
            $speciality = $student->studentProfile->speciality->name;

            $resultRows[] = [
                'ID студента' => $student->id,
                'Имя студента' => $student->name,
                'Дисциплина' => $practicePay->discipline->name,
                'Специальность' => $speciality,
                'Форма обучения' => $studyForms[$study_form],
                'Группа' => $group,
                'Язык' => $lang,
                'Сумма оплаты' => $practicePay->payed_sum,
                'Дата оплаты' => $practicePay->created_at
            ];
        }

        return Excel::create( 'practice_payed' , function($excel) use ($resultRows) {
            $excel->sheet('List 1', function($sheet) use ($resultRows)
            {
                $sheet->fromArray($resultRows);
            });
        })->download('xls');
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function exportSROPayCourses()
    {
        return view('admin.pages.export.sro_pay_courses');
    }


    /**
     * @return mixed
     */
    public function exportSROPayCoursesPost()
    {

        $aData = [];

        $aLanguage = [
            Profiles::EDUCATION_LANG_RU  => __('russian'),
            Profiles::EDUCATION_LANG_KZ  => __('kazakh'),
            Profiles::EDUCATION_LANG_EN  => __('english'),
            Profiles::EDUCATION_LANG_FR  => __('french'),
            Profiles::EDUCATION_LANG_AR  => __('arab'),
            Profiles::EDUCATION_LANG_DE  => __('german'),
        ];

        $aStatus = [
            SyllabusTaskCoursePay::STATUS_OK      => 'Ok',
            SyllabusTaskCoursePay::STATUS_PROCESS => 'В процессе'
        ];

        $oSyllabusTaskCoursePay = SyllabusTaskCoursePay::
        with('discipline')->
        with('user')->
        with('user.studentProfile')->
        with('user.studentProfile.speciality')->
        whereNull('deleted_at')->
        get();


        if( !empty($oSyllabusTaskCoursePay) && (count($oSyllabusTaskCoursePay) > 0) )
        {
            foreach( $oSyllabusTaskCoursePay as $itemSTCP )
            {
                $aData[] = [
                    'Юзер id'        => $itemSTCP->user_id,
                    'ФИО'            => $itemSTCP->user->studentProfile->fio,
                    'Специальность'  => $itemSTCP->user->studentProfile->speciality->name,
                    'Дисциплина'     => $itemSTCP->discipline->name,
                    'Язык обучения'  => $aLanguage[$itemSTCP->user->studentProfile->education_lang],
                    'Статус'         => $aStatus[$itemSTCP->status]
                ];
            }

            return Excel::create( 'sro_payed_courses' , function($excel) use ($aData) {
                $excel->sheet('List 1', function($sheet) use ($aData)
                {
                    $sheet->fromArray($aData);
                });
            })->download('xls');

        }


    }

    public function exportDiplomas()
    {
        return view('admin.pages.export.diplomas');
    }

    public function exportActivities()
    {
        $groups = StudyGroup::select('id', 'name')->get();

        return view('admin.pages.export.activities', compact('groups'));
    }
}
