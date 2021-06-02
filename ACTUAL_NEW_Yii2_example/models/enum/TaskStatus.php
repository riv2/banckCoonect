<?php
namespace app\models\enum;

use app\components\base\type\Enum;

/**
 * Статус процесса
 *
 * Class TaskStatus
 *
 * @package app\models\reference
 * @property string icon
 */
class TaskStatus extends Enum
{
    const STATUS_NEW            = 1;
    const STATUS_QUEUED         = 2;
    const STATUS_RUNNING        = 3;
    const STATUS_PAUSED         = 4;
    const STATUS_WAITING        = 5;
    const STATUS_FINISHED       = 6;
    const STATUS_CANCELED       = 7;
    const STATUS_FAILED         = 8;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Статус процесса';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Статусы процессов';
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