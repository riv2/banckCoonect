<?php

namespace api\services;

use common\models\forms\LoginForm;
use api\models\UserIdentity;
use common\models\Token;
use yii\base\ErrorException;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\User;
use yii\web\UserEvent;

/**
 * Сервис авторизации пользователя.
 * Содержит методы для авторизации и выхода пользователя.
 */
class AuthService
{
    /**
     * Авторизовать пользователя на основании заполненнной формы.
     * @param LoginForm $loginForm
     * @return bool
     * @throws ErrorException
     */
    public function login(LoginForm $loginForm): bool
    {
        if (!$loginForm->validate()) {
            Yii::$app->response->setStatusCode(422, 'Wrong login data');
            return false;
        }

        /** @var UserIdentity $user */
        $user = UserIdentity::findByEmail($loginForm->email);
        Yii::$app->user->on(User::EVENT_AFTER_LOGIN, [$this, 'afterLogin']);

        if (!Yii::$app->user->login($user)) {
            throw new ErrorException('Произошла ошибка при авторизации пользователя.');
        }

        return true;
    }

    /**
     * Выход пользователя и удаление токена.
     * @param string $authHeader
     * @return bool
     */
    public function logout(string $authHeader): bool
    {
        $userId = Yii::$app->user->id;
        if (Yii::$app->user->logout()) {
            preg_match('/^Bearer\s+(.*?)$/', $authHeader, $matches);
            Token::deleteAll(['user_id' => $userId, 'code' => ArrayHelper::getValue($matches, 1)]);
            return true;
        }

        return false;
    }

    /**
     * Действия после авторизации пользователя.
     * Обновление ключа авторизации.
     * @param UserEvent $event
     */
    public function afterLogin(UserEvent $event): void
    {
        /** @var UserIdentity $user */
        $user = $event->sender->getIdentity();
        /** @var Token $token */
        $token = Yii::createObject(Token::class);
        $token->code = Yii::$app->security->generateRandomString();
        $token->link('user', $user);
        $user->setAccessToken($token->code);
    }
}
