<?php
namespace app\models\reference;
use app\components\base\type\Reference;

class PriceFormerType extends Reference
{
    /**
     * @inheritdoc
     */
    public static function isBigData() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Тип выгружаемой цены';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Типы выгружаемой цены';
    }
}