<?php
namespace app\models\reference;
use app\components\base\type\Reference;

class ProjectTheme extends Reference
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
        return 'Тематика проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Тематики проекта';
    }

    public function crudIndexColumns()
    {
        return [
            'name'
        ];
    }
}