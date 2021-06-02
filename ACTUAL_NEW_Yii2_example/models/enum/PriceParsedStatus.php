<?php
namespace app\models\enum;
use app\components\base\type\Enum;
use app\components\ValidationRules;

/**
 * Class PriceParsedStatus
 * @package app\models\enum
 *
 * @property string id
 * @property string name
 */

class PriceParsedStatus extends Enum
{

    const STATUS_NEW              = 0;
    const STATUS_REFINED          = 1;
    const STATUS_ERROR            = 2;
    const STATUS_FILTERED_OUT     = 3;

    const COLLECTING_NEW          = 4;
    const COLLECTING_FILTERED_OUT = 5;
    const COLLECTING_IDENTIFY     = 6;
    const COLLECTING_API          = 12;

    const MATCHING_NEW            = 7;
    const MATCHING_FILTERED_OUT   = 8;
    const MATCHING_TO_MANUAL      = 9;
    const MATCHING_AUTOMATCHED    = 11;
    const ABSENT_IN_COM_ITEMS     = 10;


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