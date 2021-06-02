<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * Class ProjectExecutionStatus
 * @package app\models\enum
 *
 * Стадия выполнения проекта
 */
class ProjectExecutionStatus extends Enum
{
    const STATUS_NEW                    = 1;
    const STATUS_READY                  = 2;
    const STATUS_QUEUED                 = 3;
    const STATUS_CALCULATING            = 4;
    const STATUS_PAUSE_CALCULATING      = 5;
    const STATUS_CALCULATED             = 6;
    const STATUS_EXPORTING              = 7;
    const STATUS_PAUSE_EXPORTING        = 8;
    const STATUS_EXPORTED               = 9;
    const STATUS_CLOSED                 = 10;
    const STATUS_PREPARING              = 11;
    const STATUS_READY_TO_CALCULATE     = 12;
    const STATUS_PAUSE_PREPARING        = 13;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Статус выполнения проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Статусы выполнения проекта';
    }
}