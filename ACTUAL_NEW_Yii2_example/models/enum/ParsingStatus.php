<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * Class ParsingExecutionStatus
 * @package app\models\enum
 *
 * Статус парсинга
 */
class ParsingStatus extends Enum
{
    const STATUS_NEW                    = 1;
    const STATUS_FILE_CREATED           = 2;
    const STATUS_QUEUED                 = 3;
    const STATUS_PROCESSING             = 4;
    const STATUS_PAUSED                 = 5;
    const STATUS_DONE                   = 6;
    const STATUS_CANCELED               = 7;
    const STATUS_HANGED                 = 8;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Статус парсинга';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Статусы парсинга';
    }
}