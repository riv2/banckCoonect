<?php

return [
    'success' => [
        'title'  => 'Well done!',
        'reason' => [
            'submitted_to_post'       => 'Комментарий успещно добавлен.',
            'updated_post'            => 'Комментарий обновлен.',
            'destroy_post'            => 'Комментарий удален.',
            'destroy_from_discussion' => 'Комментарий удален.',
            'created_discussion'      => 'Новая тема успешно создана. ',
        ],
    ],
    'info' => [
        'title' => 'Heads Up!',
    ],
    'warning' => [
        'title' => 'Wuh Oh!',
    ],
    'danger'  => [
        'title'  => '',
        'reason' => [
            'errors'            => 'Пожалуйста, исправьте ошибки:',
            'prevent_spam'      => 'Чтобы предотвратить спам, интервал между сообщениями должен быть не менее :minutes мин.',
            'trouble'           => 'Извините, похоже, возникла проблема с отправкой вашего комментария.',
            'update_post'       => 'Нее ах ах ... Не удалось обновить ответ. Убедитесь, что вы не делаете ничего сомнительного.',
            'destroy_post'      => 'Нее ах ах ... Не удалось удалить ответ. Убедитесь, что вы не делаете ничего сомнительного.',
            'create_discussion' => 'Упс :( Кажется, есть проблема при создании '.mb_strtolower(trans('chatter::intro.titles.discussion')).'.',
        	'title_required'    => 'Пожалуйста, заполните заголовок',
        	'title_min'		    => 'Заголовок должен быть не менее :min символов.',
        	'title_max'		    => 'Заголовок не может быть длиннее :max символов.',
        	'content_required'  => 'Пожалуйста, напишите сообщение',
        	'content_min'  		=> 'Сообщение должно быть не менее :min символов',
        	'category_required' => 'Пожалуйста, выберите дисциплину',

       
       
        ],
    ],
];
