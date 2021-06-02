<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * Class SelectPriceLogic
 * @package app\models\enum
 */
class SelectPriceLogic extends Enum
{
    const LOGIC_A     = 101;
    const LOGIC_B     = 102;
    const LOGIC_C     = 103;
    const LOGIC_1     = 1;
    const LOGIC_2     = 2;
    const LOGIC_3     = 3;
    const LOGIC_4     = 4;
    const LOGIC_5     = 5;
    const LOGIC_6     = 6;
    const LOGIC_7     = 7;
    const LOGIC_8     = 8;
    const LOGIC_9     = 9;
    const LOGIC_10    = 10;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Алгоритм средней цены';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Алгоритмы средней цены';
    }
}