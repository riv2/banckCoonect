<?php
namespace app\models\enum;
use app\components\base\type\Enum;
use app\components\ValidationRules;

/**
 * Class HoradricCubeStatus
 * @package app\models\enum
 *
 * @property string id
 * @property string name
 */

class HoradricCubeStatus extends Enum
{

    const STATUS_NEW            = 1;
    const STATUS_MATCHED        = 2;
    const STATUS_FILTERED_OUT   = 3;
    const STATUS_WRONG          = 4;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Статус спарсеной цены';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Статусы спарсеной цены';
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