<?php
namespace app\models\enum;

use app\components\base\type\Enum;

/**
 * Тип процесса
 *
 * Class TaskType
 *
 * @package app\models\reference
 * @property string icon
 */
class TaskType extends Enum
{
    const TYPE_COMMON                   = 1;
    const TYPE_EXCHANGE_IMPORT          = 2;
    const TYPE_FILE_IMPORT              = 3;
    const TYPE_PRICE_REFINE             = 4;
    const TYPE_PROJECT_AUTO_FILL        = 5;
    const TYPE_PRICE_ORIGINS_UPDATE     = 6;
    const TYPE_PROJECT_EXECUTION        = 7;
    const TYPE_PROJECT_EXPORT           = 8;
    const TYPE_ITEM_UPDATE_URLS         = 9;
    const TYPE_PARSING                  = 10;
    const TYPE_FILE_PROCESSING          = 11;
    const TYPE_COMPETITOR_ITEM_ERRORS   = 12;
    const TYPE_START_PARSING            = 13;
    const TYPE_COMPETITOR_ITEM_UPDATE_PRICES     = 14;
    const TYPE_REPORT_KPI = 15;
    const TYPE_REPORT_MATCHING = 16;
    const TYPE_PROJECT_EXECUTION_PREPARE   = 17;
    
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Тип процесса';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Типы процессов';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['icon'], 'string'],
            ]
        );
    }
}