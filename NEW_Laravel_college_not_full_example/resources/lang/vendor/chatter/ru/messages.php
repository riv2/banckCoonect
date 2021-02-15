<?php

return [
    'words' => [
        'cancel'  => 'Отмена',
        'delete'  => 'Удалить',
        'edit'    => 'Редактировать',
        'yes'     => 'Да',
        'no'      => 'Нет',
        'minutes' => '1 минута| :count минут',
    ],

    'discussion' => [
        'new'          => 'Новая '.trans('chatter::intro.titles.discussion'),
        'all'          => 'Все '.trans('chatter::intro.titles.discussions'),
        'create'       => 'Создать тему',
        'posted_by'    => 'Сообщение от',
        'head_details' => 'Опубликовано в категории',

    ],
    'response' => [
        'confirm'     => 'Вы уверены, что хотите удалить этот комментарий?',
        'yes_confirm' => 'Да',
        'no_confirm'  => 'Нет',
        'submit'      => 'Отправить',
        'update'      => 'Обновить',
    ],

    'editor' => [
        'title'               => 'Заголовок ',
        'select'              => 'Выберите дисциплину',
        'tinymce_placeholder' => 'Введите ваше сообщение...',
        'select_color_text'   => 'Выберите цвет темы (необязательно)',
    ],

    'email' => [
        'notify' => 'Уведомить меня, когда кто-то отвечает',
    ],

    'auth' => 'Please <a href="/:home/login">login</a>
                or <a href="/:home/register">register</a>
                to leave a response.',

];
