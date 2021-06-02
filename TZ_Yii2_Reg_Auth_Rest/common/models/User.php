<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Модель для таблицы "user".
 *
 * @property int $id
 * @property int $phone Номер телефона
 * @property string $email Почта
 * @property string $first_name Имя
 * @property string $middle_name Отчество
 * @property string $last_name Фамилия
 * @property string $password_hash Хеш пароля
 * @property string $auth_key Ключ авторизации
 * @property int $created_at Дата создания
 * @property int $updated_at Дата обновления
 * 
 * @property Token[] $tokens
 */
class User extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email','phone', 'first_name', 'last_name', 'password_hash', 'auth_key', 'created_at', 'updated_at'], 'required'],
            [['email','phone', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['email','phone', 'created_at', 'updated_at'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'password_hash'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['phone', 'email'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Почта',
            'phone' => 'Номер телефона',
            'first_name' => 'Имя',
            'middle_name' => 'Отчество',
            'last_name' => 'Фамилия',
            'password_hash' => 'Хеш пароля',
            'auth_key' => 'Ключ авторизации',
            'created_at' => 'Дата создания',
            'updated_at' => 'Дата обновления',
        ];
    }


    /**
     * @return ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::class, ['user_id' => 'id']);
    }
}
