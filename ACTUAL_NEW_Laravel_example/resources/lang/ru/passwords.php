<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Ваш пароль успешно восстановлен!',
    'sent' => 'Ссылка для восстановления пароля отправлена на указанный адрес электронной почты!',
    'token' => 'Ссылка для восстановления пароля истекла, попробуйте начать процесс заново',
    'user' => 'Пользователь с данным e-mail не найден!',

    // auth/passwords/email.blade.php
    'email.app.title' => 'Сброс пароля',
    'email.email' => 'Ваш email',
    'email.send' => 'Отправить мне пароль',

    // auth/passwords/reset.blade.php
    'reset.app.title' => 'Сброс пароля',
    'reset.email' => 'Ваш email',
    'reset.password' => 'Пароль',
    'reset.password-confirm' => 'Подтвердите пароль',
    'reset.save' => 'Сохранить пароль',

    // auth/passwords/confirm.blade.php
    'confirm.app.title' => 'Подтверждение пароля',
    'confirm.message' => 'Пожалуйста, для продолжения подтвердите ваш пароль',
    'confirm.password' => 'Пароль',
    'confirm.submit' => 'Подтвердить пароль',
    'confirm.password-forgot' => 'Забыли пароль?',

];
