<?php

namespace api\models;

use common\models\UserIdentity as BaseUserIdentity;

/**
 * Расширенная модель пользователя для API на базе реализации [[yii\web\IdentityInterface]]
 */
class UserIdentity extends BaseUserIdentity
{
    /**
     * @var string Токен доступа.
     */
    protected $accessToken;

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = [
            'id',
            'first_name',
            'middle_name',
            'last_name',
        ];
        if ($this->accessToken !== null) {
            $fields['accessToken'] = function (UserIdentity $userIdentity) {
                return $userIdentity->accessToken;
            };
        }

        return $fields;
    }

    /**
     * Установить токен доступа.
     * @param string $accessToken
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}
