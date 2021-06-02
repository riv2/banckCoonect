<?php

namespace api\services;

use common\models\forms\SignupForm;
use api\models\UserIdentity;
use yii\base\ErrorException;
use Yii;

/**
 * Сервис регистрации пользователя.
 * Содержит методы, реализующие логику подготовки и сохранения данных регистрации в базу данных.
 */
class SignupService
{
    /**
     * Зарегистрировать пользователя на основании заполненнной формы.
     * @param SignupForm $form
     * @return bool
     * @throws ErrorException
     */
    public function signup(SignupForm $form): bool
    {
        if (!$form->validate()) {
            Yii::$app->response->setStatusCode(422, 'Wrong registration data');
            return false;
        }

        /** @var UserIdentity $user */
        $user = Yii::createObject(UserIdentity::class);
        $preparedData = $this->prepareSaveData($form);
        $user->setAttributes($preparedData);

        if (!$user->save(false)) {
            throw new ErrorException('Произошла ошибка при сохранении модели пользователя.');
        }

        return true;
    }

    /**
     * Подготовить данные для сохранения.
     * @param SignupForm $signupForm
     * @return array
     */
    private function prepareSaveData(SignupForm $signupForm): array
    {
        return [
            'email' => $signupForm->email,
            'first_name' => $signupForm->firstName,
            'middle_name' => $signupForm->middleName,
            'last_name' => $signupForm->lastName,
            'phone' => $signupForm->phone,
            'auth_key' => Yii::$app->security->generateRandomString(32),
            'password_hash' => Yii::$app->security->generatePasswordHash($signupForm->password),
        ];
    }
}
