<?php

return [
    'preheader'       => 'Просто сообщаю, что кто-то ответил на сообщение на форуме.',
    'greeting'        => 'Hi there,',
    'body'            => 'Просто сообщаю, что кто-то ответил на сообщение на форуме в',
    'view_discussion' => 'View the '.mb_strtolower(trans('chatter::intro.titles.discussion')).'.',
    'farewell'        => 'Хорошего дня!',
    'unsuscribe'      => [
        'message' => 'Если вы больше не хотите получать уведомления о том, что кто-то отвечает на это сообщение, обязательно снимите флажок с уведомлений в нижней части страницы.',
        'action'  => 'Не нравятся эти письма?',
        'link'    => 'Отписаться от '.mb_strtolower(trans('chatter::intro.titles.discussion')).'.',
    ],
];
