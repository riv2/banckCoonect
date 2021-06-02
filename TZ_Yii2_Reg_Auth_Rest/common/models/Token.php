<?php

namespace common\models;

use common\models\queries\TokenQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Модель для таблицы "token".
 *
 * @property string $user_id
 * @property string $code
 * @property integer $created_at
 * @property integer $within
 * @property integer $type
 *
 * @property User $user
 */
class Token extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'token';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'code', 'created_at'], 'required'],
            [['user_id', 'created_at', 'within'], 'integer'],
            [['code'], 'string', 'max' => 32],
            [['user_id', 'code'], 'unique', 'targetAttribute' => ['user_id', 'code'], 'message' => 'The combination of Пользователь and Токен has already been taken.'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Пользователь',
            'code' => 'Токен',
            'created_at' => 'Дата создания',
            'within' => 'Время действия',
        ];
    }

    /**
     * @return bool Истекло ли время жизни токена.
     */
    public function getIsExpired()
    {
        return $this->within > 0 && ($this->created_at + $this->within) < time();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($insert) {
            $this->setAttribute('created_at', time());
            $this->setAttribute('code', Yii::$app->security->generateRandomString());
        }

        return parent::beforeSave($insert);
    }

    /**
     * Удаление токена, если срок жизни истек.
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if ($this->within > 0 && $this->getIsExpired()) {
            $this->delete();
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public static function primaryKey()
    {
        return ['user_id', 'code'];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return TokenQuery
     */
    public static function find()
    {
        return Yii::createObject(TokenQuery::class, [get_called_class()]);
    }
}
