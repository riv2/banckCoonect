<?php

namespace app\components\base\type;
use app\components\base\BaseModel;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\Status;
use app\models\reference\User;
use yii;
use yii\helpers\ArrayHelper;

/**
 * Class Register
 * @package app\components\base\type
 * @property string id
 * @property int status_id
 * @property DateTime updated_at
 * @property DateTime created_at
 * @property int created_user_id
 * @property int updated_user_id
 *
 * @property User   createdUser
 * @property User   updatedUser
 * @property Status status
 */
class Register extends  BaseModel
{
    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }

    public $labelAttribute = 'id';
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('id'),
            ValidationRules::ruleDateTime('created_at', 'updated_at'),
            ValidationRules::ruleDefault('status_id', Status::STATUS_ACTIVE),
            ValidationRules::ruleEnum('status_id', Status::className())
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id'                => 'ID',
                'status_id'         => 'ID Состояние',
                'created_at'        => 'Дата создания',
                'updated_at'        => 'Дата изменения',
                'created_user_id'   => 'ID Создал',
                'updated_user_id'   => 'ID Изменил',
                'status'            => 'Состояние',
                'createdUser'       => 'Создал',
                'updatedUser'       => 'Изменил',
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function excludeFieldsUpdate() {
        return array_merge(parent::excludeFieldsUpdate(),[
            'status',
            'createdUser',
            'updatedUser',
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function fileImportPresetColumns()
    {
        return [
        ];
    }
    /**
     * @inheritdoc
     */
    public function excludeFieldsCreate() {
        return array_merge(parent::excludeFieldsCreate(),[
            'status',
            'createdUser',
            'updatedUser',
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
        ]);
    }

    /**
     * Не импортировать колонки
     * @return array
     */
    public function excludeFieldsFileImportColumns() {
        return [
            'status',
            'createdUser',
            'updatedUser',
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
            'status_id',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = new DateTime();
            }
            $this->updated_at = new DateTime();
            if (!(Yii::$app instanceof \yii\console\Application)) {
                if ($insert) {
                    if (!$this->created_user_id && Yii::$app->get('user', false)) {
                        $this->created_user_id = Yii::$app->user->id;
                    }
                }
                if (Yii::$app->get('user', false)) {
                    $this->updated_user_id = Yii::$app->user->id;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return isset($this->name)?$this->name:$this->id;
    }


    public function getDefaultOrderColumns() {
        return [
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return [
            'status',
            'createdUser',
            'updatedUser',
        ];
    }
    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }
    
    /**
     *
     * @return User
     */
    public function getCreatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_user_id']);
    }

    /**
     * @return User
     */
    public function getUpdatedUser()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_user_id']);
    }

    /**
     * @param array $attributes
     * @param string $condition
     * @param array $params
     * @return int
     * @throws yii\base\InvalidConfigException
     */
    public static function updateAll($attributes, $condition = '', $params = [])
    {
        if (!(Yii::$app instanceof \yii\console\Application)) {
            if (!isset($attributes['updated_user_id']) && Yii::$app->get('user', false)) {
                $attributes['updated_user_id'] = Yii::$app->user->id;
            }
        }
        return parent::updateAll($attributes, $condition, $params);
    }
}