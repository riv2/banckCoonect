<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * Class CompetitionMode
 * @package app\models\enum
 */
class CompetitionMode extends Enum
{
    const MODE_COMPETITORS       = 1;
    const MODE_ALL_KNOWN_SHOPS   = 2;
    const MODE_ALL_SHOPS         = 3;
    const MODE_ALL_SHOPS_MIN     = 4;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Режим выбора конкурентов';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Режимы выбора конкурентов';
    }
}