<?php
namespace app\models\register;

use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\ValidationRules;
use yii\helpers\ArrayHelper;

/**
 * Class ExchangeExport
 * @package app\models\register
 *
 * @property boolean is_error
 * @property string local_id              => 'ID сущности',
 * @property string entity_id             => 'Сущность',
 * @property string exchange_system_id    => 'Внешняя система',
 *
 *
 */
class ExchangeExport extends Register
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Регистр экспорта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Регистр экспорта';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('entity_id'),
            ValidationRules::ruleRequired('entity_id', 'local_id', 'exchange_system_id'),
            [
                [['is_error'], 'boolean'],
                [['local_id', 'exchange_system_id'], 'string'],
            ],
            ValidationRules::ruleDefault('is_error', false),
            ValidationRules::ruleEnum('entity_id', Entity::className())
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'is_error'              => 'Неудачный экспорт',
                'local_id'              => 'ID сущности',
                'entity_id'             => 'Сущность',
                'exchange_system_id'    => 'Внешняя система',
            ]
        );
    }
}