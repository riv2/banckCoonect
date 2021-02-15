<?php

use Illuminate\Database\Seeder;

class EmployeesPositionRequirementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    	$requirements = [

    														/* PERSONAL INFO SECTION */

    		[
    			'name' => 'ФИО (рус)', 
    			'field_type' => 'text', 
    			'field_name' => 'fio_ru',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'ФИО (каз)', 
    			'field_type' => 'text', 
    			'field_name' => 'fio_kz',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'ФИО (англ)', 
    			'field_type' => 'text', 
    			'field_name' => 'fio_en',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Гражданство', 
    			'field_type' => 'text', 
    			'field_name' => 'citizenship',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'ИИН', 
    			'field_type' => 'text', 
    			'field_name' => 'iin',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Дата рождения', 
    			'field_type' => 'date', 
    			'field_name' => 'bdate',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Пол', 
    			'field_type' => 'select', 
    			'field_name' => 'sex',
    			'category' => 'personal_info',
                'multiple' => false,
                'options' => [
                    'Мужчина',
                    'Женщина'
                ]
    		],
    		[
    			'name' => 'Национальность', 
    			'field_type' => 'text', 
    			'field_name' => 'nationality',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Семейное положение', 
    			'field_type' => 'select', 
    			'field_name' => 'family_status',
    			'category' => 'personal_info',
                'multiple' => false,
                'options' => [
                    'Холост(ая)',
                    'Помолвлен(а)'
                ]
    		],
    		[
    			'name' => 'Адрес прописки', 
    			'field_type' => 'text', 
    			'field_name' => 'address_registration',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Адрес проживания', 
    			'field_type' => 'text', 
    			'field_name' => 'address_residence',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Номер телефона мобильный', 
    			'field_type' => 'text', 
    			'field_name' => 'mobile_phone',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Номер телефона домашний', 
    			'field_type' => 'text', 
    			'field_name' => 'home_phone',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Email', 
    			'field_type' => 'text', 
    			'field_name' => 'email',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Резюме', 
    			'field_type' => 'file', 
    			'field_name' => 'resume',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Приписное свид-во', 
    			'field_type' => 'file', 
    			'field_name' => 'registered_certificate',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Справка об отсутствии судимости', 
    			'field_type' => 'file', 
    			'field_name' => 'certificate_no_criminal_record',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Справка 086', 
    			'field_type' => 'file', 
    			'field_name' => 'reference_086',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Справка из наркодиспансера', 
    			'field_type' => 'file', 
    			'field_name' => 'certificate_from_drug_dispensary',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Договор с ЕНПФ', 
    			'field_type' => 'file', 
    			'field_name' => 'contract_with_enpf',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Заявление на Конкурсную комиссию', 
    			'field_type' => 'file', 
    			'field_name' => 'application_for_tender_commission',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Заявление  о приеме на работу', 
    			'field_type' => 'file', 
    			'field_name' => 'application_for_job',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Номер IBAN', 
    			'field_type' => 'file', 
    			'field_name' => 'IBAN_number',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Личный листок (по форме)', 
    			'field_type' => 'file', 
    			'field_name' => 'personal_sheet_in_form',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Автобиография / резюме', 
    			'field_type' => 'file', 
    			'field_name' => 'autobiography_resume',
    			'category' => 'personal_info',
                'multiple' => false
    		],
    		[
    			'name' => 'Трудовая книжка', 
    			'field_type' => 'file', 
    			'field_name' => 'employment_history',
    			'category' => 'personal_info',
                'multiple' => false
    		],

                                                            /* QUALIFICATION INCREASE SECTION */

            [
                'name' => 'Повышение квалификации', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'qualification_increase',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Вид документа', 
                        'field_type' => 'select',
                        'field_name' => 'doc_type',
                        'options' => [
                            'Сертификат',
                            'Диплом',
                            'Свидетельство'
                        ]
                    ],
                    [
                        'name' => 'Наименование курса',
                        'field_type' => 'text',
                        'field_name' => 'course_name',
                        'options' => ''
                    ],
                    [
                        'name' => 'Кол-во часов курса',
                        'field_type' => 'text',
                        'field_name' => 'course_hours',
                        'options' => ''
                    ],
                    [
                        'name' => 'Дата выдачи документа',
                        'field_type' => 'date',
                        'field_name' => 'date_issue_document',
                        'options' => ''
                    ],
                    [
                        'name' => 'Прикрепление файла',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ]
                ]
            ],

    														/* EDUCATION SECTION */

    		[
    			'name' => 'Бакалавриат', 
    			'field_type' => 'json', 
    			'field_name' => 'json',
    			'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подгрузка файлов ( диплом + приложение)',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                ]
    		],
            [
                'name' => 'Специалитет', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подгрузка файлов ( диплом + приложение)',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Магистратура', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подгрузка файлов ( диплом + приложение)',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Кандидат наук', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Подгрузка файлов',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Доктор наук', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Подгрузка файлов',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Doctor PHD', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Подгрузка файлов',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Ученые звания', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Подгрузка файлов',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                    [
                        'name' => 'Область науки',
                        'field_type' => 'text',
                        'field_name' => 'branch_of_science',
                        'options' => ''
                    ],
                    [
                        'name' => 'Специальность',
                        'field_type' => 'text',
                        'field_name' => 'education_specialty',
                        'options' => ''
                    ],
                    [
                        'name' => 'Начало обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'start_education',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец обучения', 
                        'field_type' => 'date', 
                        'field_name' => 'end_education',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Знание языков', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Выбор языка',
                        'field_type' => 'text',
                        'field_name' => 'language',
                        'options' => ''
                    ],
                    [
                        'name' => 'Уровень владения',
                        'field_type' => 'text',
                        'field_name' => 'language_level',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подтверждающий документ',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ]
                ]
            ],
    		[
                'name' => 'Компьютерные навыки', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Описание',
                        'field_type' => 'text',
                        'field_name' => 'description',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подтверждающий документ',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ]
                ]
            ],
            [
                'name' => 'Дополнительные  навыки', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'education',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Описание',
                        'field_type' => 'text',
                        'field_name' => 'description',
                        'options' => ''
                    ],
                    [
                        'name' => 'Подтверждающий документ',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ]
                ]
            ],

    														/* seniority */

    		[
                'name' => 'Трудовой стаж', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'seniority',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Начало работы', 
                        'field_type' => 'date', 
                        'field_name' => 'start_work',
                        'options' => ''
                    ],
                    [
                        'name' => 'Конец работы', 
                        'field_type' => 'date', 
                        'field_name' => 'end_work',
                        'options' => ''
                    ],
                    [
                        'name' => 'Место работы',
                        'field_type' => 'text',
                        'field_name' => 'workplace',
                        'options' => ''
                    ],
                    [
                        'name' => 'Занимаемая должность',
                        'field_type' => 'text',
                        'field_name' => 'position',
                        'options' => ''
                    ],
                    [
                        'name' => 'Основные функциональные обязанности',
                        'field_type' => 'text',
                        'field_name' => 'requirements',
                        'options' => ''
                    ],
                    [
                        'name' => 'Отметка о преподавательском стаже',
                        'field_type' => 'text',
                        'field_name' => 'teacher',
                        'options' => ''
                    ],
                    [
                        'name' => 'Контакты руководителя ( организации)',
                        'field_type' => 'text',
                        'field_name' => 'contacts',
                        'options' => ''
                    ]
                ]
            ],

    														/* НИР */

    		[
    			'name' => 'Направление научной деятельности', 
    			'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'nir',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Описание',
                        'field_type' => 'text',
                        'field_name' => 'description',
                        'options' => ''
                    ]
                ]
    		],
    		[
                'name' => 'Список научных трудов', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'nir',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Подгрузка файла',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ]
                ]
            ],
            [
                'name' => 'Печатное издание', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'nir',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Вид издания',
                        'field_type' => 'select',
                        'field_name' => 'type',
                        'options' => [
                            'научная статья',
                            'публикация',
                            'монография',
                            'учебник',
                            'учебное пособие',
                            'глоссарий',
                            'справочник',
                            'прочее'
                        ]
                    ],
                    [
                        'name' => 'Наименование',
                        'field_type' => 'text',
                        'field_name' => 'name',
                        'options' => ''
                    ],
                    [
                        'name' => 'Размещение',
                        'field_type' => 'select',
                        'field_name' => 'publication',
                        'options' => [
                            'Рекомендованные ККСОН МОН РК',
                            'Web of Science (Clarivate Analitics)',
                            'Scopus (Elsevier)',
                            'РИНЦ',
                            'прочие'
                        ]
                    ],
                    [
                        'name' => 'Год публикации',
                        'field_type' => 'text',
                        'field_name' => 'year',
                        'options' => ''
                    ],
                    [
                        'name' => 'Объем п.л.',
                        'field_type' => 'text',
                        'field_name' => 'size',
                        'options' => ''
                    ],
                    [
                        'name' => 'Номер издания',
                        'field_type' => 'text',
                        'field_name' => 'edition_number',
                        'options' => ''
                    ],
                    [
                        'name' => 'Прикрепить файл',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                    [
                        'name' => 'Соавторы',
                        'field_type' => 'text',
                        'field_name' => 'collaborators',
                        'options' => ''
                    ],
                    [
                        'name' => 'Язык издания',
                        'field_type' => 'select',
                        'field_name' => 'language',
                        'options' => [
                            'казахский',
                            'русский',
                            'английский',
                            'другой'
                        ]
                    ],
                    [
                        'name' => 'Соавторство (да\нет)',
                        'field_type' => 'text',
                        'field_name' => 'co_authorship',
                        'options' => ''
                    ],
                    [
                        'name' => 'Наличие ISBN (да/нет, при наличии вбить  ISBN)',
                        'field_type' => 'text',
                        'field_name' => 'isbn',
                        'options' => ''
                    ],
                ]
            ],
            [
                'name' => 'Проект', 
                'field_type' => 'json', 
                'field_name' => 'json',
                'category' => 'nir',
                'multiple' => true,
                'json' => [
                    [
                        'name' => 'Тема исследования',
                        'field_type' => 'text',
                        'field_name' => 'topic',
                        'options' => ''
                    ],
                    [
                        'name' => 'Направление',
                        'field_type' => 'text',
                        'field_name' => 'branch',
                        'options' => ''
                    ],
                    [
                        'name' => 'Сроки реализации',
                        'field_type' => 'text',
                        'field_name' => 'range_time',
                        'options' => ''
                    ],
                    [
                        'name' => 'Источник финансирования',
                        'field_type' => 'text',
                        'field_name' => 'financing',
                        'options' => ''
                    ],
                    [
                        'name' => 'Роль',
                        'field_type' => 'select',
                        'field_name' => 'role',
                        'options' => [
                            'руководитель',
                            'исполнитель'
                        ]
                    ],
                    [
                        'name' => 'Наличие патента или др. результатов',
                        'field_type' => 'text',
                        'field_name' => 'achievements',
                        'options' => ''
                    ],
                    [
                        'name' => 'Прикрепить файл',
                        'field_type' => 'file',
                        'field_name' => 'file',
                        'options' => ''
                    ],
                ]
            ],
    	];

        \App\EmployeesRequirement::truncate();
        \App\EmployeesRequirementsField::truncate();
        
        foreach ($requirements as $requirement)
        {
            $insertRecord = [
                'name'       => $requirement['name'],
                'field_type' => $requirement['field_type'],
                'field_name' => $requirement['field_name'],
                'category'   => $requirement['category'],
                'multiple'   => $requirement['multiple'],
            ];

            if(array_key_exists('options', $requirement)){
                $insertRecord += ['options' => $requirement['options']];
            }

            $record = \App\EmployeesRequirement::create($insertRecord);

            if(array_key_exists('json', $requirement)){
                foreach ($requirement['json'] as $value) {
                    \App\EmployeesRequirementsField::create([
                        'requirement_id' => $record->id,
                        'name'           => $value['name'],
                        'field_type'     => $value['field_type'],
                        'field_name'     => $value['field_name'],
                        'options'        => $value['options']
                    ]);
                }
            }
        }
    }
}
