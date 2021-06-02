<?php
namespace app\models\enum;
use app\components\base\type\Enum;

/**
 * @property string id
 * @property string name
 */
class ErrorType extends Enum
{
    const TYPE_COMMON         = 0;
    const TYPE_IMPORT         = 1;
    const TYPE_EXPORT         = 2;
    const TYPE_FILE_IMPORT    = 3;
    const TYPE_FILE_EXPORT    = 4;
    const TYPE_TASK           = 5;

    const TYPE_PARSING                  = 6;
    const TYPE_PARSED_PRICE_IMPORTING   = 7;
    const TYPE_PARSED_PRICE_REFINING    = 8;

    const TYPE_EXTERNAL_API = 9;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Тип ошибки';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Типы ошибки';
    }
}