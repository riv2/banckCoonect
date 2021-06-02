<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * Class Source
 * @package app\models\enum
 */
class Source extends Enum
{
    const SOURCE_YANDEX_MARKET     = 1;
    const SOURCE_WEBSITE           = 2;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Торговая площадка';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Торговые площадки';
    }
}