<?php
namespace app\models\enum;
use app\components\base\type\Enum;
use app\components\ValidationRules;

/**
 * Class Status
 * @package app\models\enum
 *
 * @property string id
 * @property string name
 */

class Status extends Enum
{
    const STATUS_REMOVED        = 1;
    const STATUS_ACTIVE         = 0;
    const STATUS_DISABLED       = 2;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Статус';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Статусы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('name'),
            [
                [['name'], 'string'],
                [['id'], 'number', 'integerOnly' => true, 'except' => self::SCENARIO_SEARCH],
                [['id'], 'safe', 'on' => self::SCENARIO_SEARCH],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'            => 'ID',
            'name'          => 'Название',
        ];
    }

    /**
     * @inheritdoc
     */
    public function excludeFieldsFileImportColumns() {
        return [
        ];
    }


    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return [
        ];
    }

    /**
     * @return Status
     */
    public function getStatus()
    {
        return null;
    }
}