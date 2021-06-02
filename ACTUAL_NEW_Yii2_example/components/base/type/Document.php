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
 * Class Document
 * @package app\components\base\type
 * @property string id
 * @property int number
 * @property string name
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
class Document extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function noCount() {
        return false;
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('id'),
            [
                [['name'], 'string'],
                [['number'], 'number', 'integerOnly' => true , 'except' => self::SCENARIO_SEARCH],
                [['number'], 'safe', 'on' => self::SCENARIO_SEARCH],
            ],
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
                'name'              => 'Документ',
                'number'            => 'Номер',
                'status_id'         => 'ID Состояния',
                'created_at'        => 'Дата создания',
                'updated_at'        => 'Дата изменения',
                'created_user_id'   => 'ID создателя',
                'updated_user_id'   => 'ID изменившего',
                'status'            => 'Состояние',
                'createdUser'   => 'Создал',
                'updatedUser'   => 'Изменил',
            ]
        );
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
    public function excludeFieldsUpdate() {
        return array_merge(parent::excludeFieldsUpdate(),[
            'number',
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
            'status',
            'createdUser',
            'updatedUser',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function excludeFieldsCreate() {
        return array_merge(parent::excludeFieldsCreate(),[
            'number',
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
            'status',
            'createdUser',
            'updatedUser',
        ]);
    }

    /**
     * Не импортировать колонки
     * @return array
     */
    public function excludeFieldsFileImportColumns() {
        return [
            'created_at',
            'updated_at',
            'created_user_id',
            'updated_user_id',
            'status_id',
            'status',
            'createdUser',
            'updatedUser',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (!$this->name) {
                $this->name = $this->getSingularNominativeName();
            }
            if ($insert) {
                $this->created_at = new DateTime();
                if (!$this->created_user_id&& method_exists(Yii::$app, 'getSession') && isset(Yii::$app->user)  && !Yii::$app->user->isGuest) {
                    $this->created_user_id = Yii::$app->user->identity->getId();
                }
            }
            $this->updated_at = new DateTime();
            if (isset(Yii::$app->user)&& method_exists(Yii::$app, 'getSession') && !Yii::$app->user->isGuest) {
                $this->updated_user_id = Yii::$app->user->identity->getId();
            }
            return true;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name.' №'.$this->number;
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
}