<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use yii\helpers\Html;

/**
 * Модель настройки
 *
 * @property string data
 */
class Setting extends Reference
{

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Настройка';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Настройки';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'data'  => 'Значение',
            'full_name' => 'Наименование',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return [
            'full_name',
            'data',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired(['data']),
            [
                [['data', 'full_name'], 'string'],
            ]
        );
    }

    /**
     * Получение значения настройки
     * @param string $name название
     * @param null $defaultValue
     * @return string|null
     */
    public static function getValue($name, $defaultValue = null)
    {
        $value = self::find()->select('data')->andWhere(['name' => $name])->scalar();
        return !is_null($value) ? $value : $defaultValue;
    }


}