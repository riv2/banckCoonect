<?php
/**
 * Created by Vlad.
 * User: user
 * Date: 09.12.19
 * Time: 12:06
 */

namespace App\Services;

use App\{
    EmployeesUser,
    ManualCitizenship,
    Nationality,
    Speciality
};
use Carbon\Carbon;
use PhpOffice\PhpWord\TemplateProcessor;

class EmployeesDecree
{
    public static $standartFields = [
        'iin'                    => 'ИИН', 
        'email'                  => 'Email', 
        'status'                 => 'Статус', 
        'bdate'                  => 'День рождения', 
        'address_registration'   => 'Адрес прописки', 
        'address_residence'      => 'Адрес проживания', 
        'home_phone'             => 'Домашний телефон', 
        'mobile'                 => 'Мобильный телефон',
        'doctype'                => 'Тип документа, удостоверяющего личность',
        'docnumber'              => 'Номер документа, удостоверяющего личность', 
        'issuing'                => 'Кем выдан документ удостоверяющий личность', 
        'issuedate'              => 'Дата выдачи документа, удостоверяющего личность', 
        'expire_date'            => 'Срок действия документа, удостоверяющего личность', 
        'education_degree'       => 'Степень',
        'nostrification'         => 'Нострификация',
        'education_lang'         => 'Язык Обучения',
        'end_education'          => 'Конец обучения',
        'protocol_number'        => 'Номер протокола',
        'start_education'        => 'Начало обучения',
        'dissertation_topic'     => 'Тема диссертации',
        'institution'            => 'Учебное заведение',
        'qualification_assigned' => 'Присвоеная квалификация',
        'experience_type'        => 'Тип стажа',
        'job'                    => 'Место работы',
        'part_time_job'          => 'Показатель Совместительства',
        'teacher_start_date'     => 'Дата начала преподавательской деятельности',
        'teacher_end_date'       => 'Дата окончания преподавательской деятельности',
        'content'                => 'Содержание',
        'publication_date'       => 'Дата издания',
        'impact_factor'          => 'Импакт фактор',
        'science_branch'         => 'Область науки',
        'theme'                  => 'Тема публикации',
        'info'                   => 'доп. Информация',
        'publication_name'       => 'Наименование издания',
    ];

    public static $specificFields = [
        'sex' => [
            0 => 'Женщина',
            1 => 'Мужчина',
        ],
        'family_status' => [
            'single'  => 'Холост(ая)',
            'marital' => 'Помолвлен(а)',
            ''        => 'Не установлено'
        ]
    ];

    public function employeesEditData($data){
        $changes = [];
        $templateProcessor = new TemplateProcessor(resource_path('docx/employees_decree.docx'));

        foreach ($data['data'] as $key => $value) {
            if( $key == 'sex' ) {
                $changes[] = [ 'field' => 'Пол', 'action' => 'Изменено c ' . self::$specificFields['sex'][$value['old']] . ' на ' . self::$specificFields['sex'][$value['new']]];
            } elseif( $key == 'family_status' ) {
                $changes[] = [ 'field' => 'Семейное положение', 'action' => 'Изменено c ' . self::$specificFields['family_status'][$value['old']] . ' на ' . self::$specificFields['family_status'][$value['new']]];
            } elseif( $key == 'citizenship' ) {
                $changes[] = [ 'field' => 'Гражданство', 'action' => 'Изменено c ' . ManualCitizenship::where('id', $value['old'])->value('name') . ' на ' . ManualCitizenship::where('id', $value['new'])->value('name')];
            } elseif( $key == 'nationality_id' ) {
                $changes[] = [ 'field' => 'Национальность', 'action' => 'c ' . Nationality::where('id', $value['old'])->value('name_ru') . ' на ' . Nationality::where('id', $value['new'])->value('name_ru')];
            } elseif( $key == 'education_speciality_id' ) {
                $changes[] = [ 'field' => 'Специальность', 'action' => 'Изменено c ' . Speciality::where('id', $value['old'])->value('name') . ' на ' . Speciality::where('id', $value['new'])->value('name')];
            } else {
                $changes[] = [ 'field' => self::$standartFields[$key], 'action' => 'Изменено c ' . $value['old'] . ' на ' . $value['new']];
            }
        }

        $templateProcessor->setValues(['user_name' => $data['user']['name']]);
        $templateProcessor->cloneBlock('foreach_changes', 0, true, false, $changes);
        
        $fileName = 'EmployeesDecree_'.Carbon::now()->format('Y_m_d_H_i_s').'.docx';

        $templateProcessor->saveAs(storage_path('app/employees/users/decree/'.$fileName));
        
        return $fileName;
    }

    public function approveCandidate($data){
        $templateProcessor = new TemplateProcessor(resource_path('docx/candidate_approved_decree.docx'));

        foreach ($data as $key => $value) {
            $templateProcessor->setValues([$key => $value]);
        }

        $fileName = 'CandidateApprovedDecree_'.Carbon::now()->format('Y_m_d_H_i_s').'.docx';

        $templateProcessor->saveAs(storage_path('app/employees/users/decree/'.$fileName));
        
        return $fileName;
    }
}