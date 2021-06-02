<?php
namespace app\models\enum;
use app\components\base\type\Enum;

class PriceExportMode extends Enum
{
    const MODE_ALL              = 1;
    const MODE_NO_RRP_ONLY      = 2;
    const MODE_WITH_RRP_ONLY    = 3;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Режим выгрузки цен';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Режимы выгрузки цен';
    }
}