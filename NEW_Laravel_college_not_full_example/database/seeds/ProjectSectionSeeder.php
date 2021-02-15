<?php

use App\ProjectSection;
use Illuminate\Database\Seeder;

class ProjectSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sectionList = [
            [
                'url' => 'articles',
                'name_ru' => 'Статьи'
            ],
            [
                'url' => 'services',
                'name_ru' => 'Услуги'
            ],
            [
                'url' => 'roles',
                'name_ru' => 'Роли'
            ],
            [
                'url' => 'users',
                'name_ru' => 'Пользователи'
            ],
            [
                'url' => 'teachers',
                'name_ru' => 'Преподаватели'
            ],
            [
                'url' => 'courses',
                'name_ru' => 'Курсы'
            ],
            [
                'url' => 'trends',
                'name_ru' => 'Направления'
            ],
            [
                'url' => 'specialities',
                'name_ru' => 'Специальности'
            ],
            [
                'url' => 'disciplines',
                'name_ru' => 'Дисциплины'
            ],
            [
                'url' => 'themes',
                'name_ru' => 'Силлабус(темы)'
            ],
            [
                'url' => 'entrance_tests',
                'name_ru' => 'Вступительные экзамены'
            ],
            [
                'url' => 'buildings',
                'name_ru' => 'Здания'
            ],
            [
                'url' => 'rooms',
                'name_ru' => 'Аудитории'
            ],
            [
                'url' => 'helps',
                'name_ru' => 'Помощь'
            ],
            [
                'url' => 'promotions',
                'name_ru' => 'Акции'
            ],
            [
                'url' => 'inspection',
                'name_ru' => 'Приемка'
            ],
            [
                'url' => 'students',
                'name_ru' => 'Студенты'
            ],
            [
                'url' => 'modules',
                'name_ru' => 'Модули'
            ],
            [
                'url' => 'or_cabinet',
                'name_ru' => 'Кабинет ОР'
            ],
            [
                'url' => 'news',
                'name_ru' => 'Объявления'
            ],
            [
                'url' => 'orders',
                'name_ru' => 'Приказы'
            ],
            [
                'url' => 'discountrequests',
                'name_ru' => 'Заявки на акциии'
            ],
            [
                'url' => 'export_students',
                'name_ru' => 'Выгрузки: студенты'
            ],
            [
                'url' => 'guests',
                'name_ru' => 'Гости'
            ],
            [
                'url' => 'discipline_pay_cancel',
                'name_ru' => 'Отмена покупки дисциплин'
            ],
            [
                'url'       => 'employees',
                'name_ru'   => 'Кадры'
            ],
            [
                'url'       => 'manuals',
                'name_ru'   => 'Справочники'
            ],
            [
                'url'       => 'quiz',
                'name_ru'   => 'Анкетирование'
            ],
            [
                'url' => 'appeals',
                'name_ru' => 'Апелляции'
            ],
            [
                'url'       => 'applications',
                'name_ru'   => 'Заявления'
            ],
            [
                'url' => 'export_exam_sheet',
                'name_ru' => 'Экспорт экз. ведомости'
            ],
            [
                'url' => 'discipline_practice_upload',
                'name_ru' => 'Выгрузка практик дисциплин'
            ],
            [
                'url'       => 'practice',
                'name_ru'   => 'Практика'
            ],
            [
                'url' => 'discipline_practice_upload',
                'name_ru' => 'Выгрузка практик дисциплин'
            ],
            [
                'url'       => 'appeals',
                'name_ru'   => 'Апелляции'
            ],
            [
                'url'       => 'applications',
                'name_ru'   => 'Заявления'
            ],
            [
                'url' => 'discipline_practice_upload',
                'name_ru' => 'Выгрузка практик дисциплин'
            ],
            [
                'url' => 'quiz_results',
                'name_ru' => 'Результаты тестов'
            ],
            [
                'url'       => 'employees',
                'name_ru'   => 'Кадры'
            ],
            [
                'url'       => 'manuals',
                'name_ru'   => 'Справочники'
            ],
            [
                'url' => 'visits',
                'name_ru' => 'Посещаемость'
            ],
            [
                'url' => 'etxt',
                'name_ru' => 'Проверка на плагиат'
            ],
            [
                'url' => 'study_plan',
                'name_ru' => 'Учебный план'
            ],
            [
                'url' => 'speciality_semesters',
                'name_ru' => 'Спец. семестры'
            ],
            [
                'url' => 'export_student_result',
                'name_ru' => 'Выгрузка результатов студентов'
            ],
            [
                'url' => 'library',
                'name_ru' => 'Библиотека'
            ],
            [
                'url' => 'webcam',
                'name_ru' => 'Просмотр видеозаписей экзаменов'
            ],
            [
                'url' => 'export_diplomas',
                'name_ru' => 'Архив выдачи дипломов'
            ],
            [
                'url' => 'speciality_prices',
                'name_ru' => 'Спец. цены'
            ],
            [
                'url' => 'check_plagiarism_result',
                'name_ru' => 'Результаты проверки уникальности'
            ],
            [
                'url' => 'nomenclature',
                'name_ru' => 'Номенклатура'
            ],
            [
                'url' => 'activities',
                'name_ru' => 'Проверка активности'
            ],
            [
                'url' => 'info_table',
                'name_ru' => 'Infodesk'
            ],
            [
                'url' => 'info_news',
                'name_ru' => 'Расписание'
            ],
            [
                'url' => 'activities',
                'name_ru' => 'Проверка активности'
            ],
            [
                'url' => 'export_activities',
                'name_ru' => 'Выгрузка активности'
            ],
            [
                'url' => 'forum',
                'name_ru' => 'Форум'
            ],
            [
                'url' => 'assign_teachers',
                'name_ru' => 'Назначить преподавателей'
            ],
            [
                'url' => 'teacher_journal',
                'name_ru' => 'Журнал преподавателя'
            ]
        ];

        foreach ($sectionList as $k => $item) {
            $section = ProjectSection::where('url', $item)->first();

            if (empty($section)) {
                $section = new ProjectSection();
                $section->project = ProjectSection::PROJECT_ADMIN;
                $section->url = $item['url'];
            }

            $section->name_ru = $item['name_ru'];
            $section->save();
        }
    }
}
