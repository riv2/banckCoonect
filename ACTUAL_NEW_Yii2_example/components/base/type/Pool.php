<?php

namespace app\components\base\type;
use app\components\base\BaseModel;
use app\components\DateTime;
use app\components\ValidationRules;
use yii;
use yii\helpers\ArrayHelper;

/**
 * Class Pool
 * @package app\components\base\type
 * @property string id
 * @property DateTime created_at
 */
class Pool extends  BaseModel
{

    public $labelAttribute = 'id';

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
            ValidationRules::ruleDateTime('created_at')
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
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'id'                => 'ID',
                'created_at'        => 'Дата создания',
            ]
        );
    }
    
    /**
     * @inheritdoc
     */
    public function excludeFieldsUpdate() {
        return array_merge(parent::excludeFieldsUpdate(),[
            'created_at',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function excludeFieldsCreate() {
        return array_merge(parent::excludeFieldsCreate(),[
            'created_at',
        ]);
    }

    /**
     * Не импортировать колонки
     * @return array
     */
    public function excludeFieldsFileImportColumns() {
        return [
            'created_at',
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                if (!$this->created_at) {
                    $this->created_at = new DateTime();
                }
                if (!$this->id) {
                    $this->id = static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar();
                }
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
        return '';
    }


    public function getDefaultOrderColumns() {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return [
        ];
    }
}