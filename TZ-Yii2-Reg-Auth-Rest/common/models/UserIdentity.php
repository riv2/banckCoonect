<?php
namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * Расширенная модель пользователя, реализующая [[yii\web\IdentityInterface]]
 */
class UserIdentity extends User implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var Token $tokenModel */
        $tokenModel = Token::find()->byCode($token)->one();
        if ($tokenModel === null) {
            return null;
        }

        return static::findIdentity($tokenModel->user_id);
    }

    /**
     * Найти пользователя по номеру телефона.
     * @param $phone
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Валидировать пароль.
     * @param string $password Пароль для валидации.
     * @return bool Пароль валидный.
     */
    public function validatePassword($password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
