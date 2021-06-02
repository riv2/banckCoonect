<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 19.06.16
 * Time: 22:11
 */

namespace app\models\enum;


use app\components\base\type\Enum;

/**
 * Class FileFormat
 * @package app\models\enum
 *
 * @property string extensions
 * @property string mine_types
 */
class FileFormat extends Enum
{
    const TYPE_CSV         = 1;
    const TYPE_XLS         = 2;
    const TYPE_JSON        = 3;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Формат файла';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Формат файла';
    }
}