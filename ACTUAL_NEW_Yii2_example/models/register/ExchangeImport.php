<?php
namespace app\models\register;

use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\ValidationRules;

/**
 * Class ExchangeImport
 * @package app\models\register
 *
 * @property string     remote_id
 * @property string     remote_entity
 * @property boolean    is_error
 * @property string     error_message
 * @property string     error_id
 * @property int        requester_entity_id
 * @property string     requester_id
 *
 *
 */
class ExchangeImport extends Register
{
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Регистр импорта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Регистр импорта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleRequired('remote_id', 'remote_entity'),
            ValidationRules::ruleUuid('error_id'),
            ValidationRules::ruleUuid('requester_id'),
            [
                [['is_error'], 'boolean'],
                [['remote_id', 'remote_entity', 'error_message'], 'string'],
            ],
            ValidationRules::ruleDefault('is_error', false),
            ValidationRules::ruleEnum('requester_entity_id', Entity::className())
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'remote_id'         => 'Внешний ID',
                'remote_entity'     => 'Внешняя сущность',
                'exchange_system_id'=> 'Система',
                'is_error'          => 'Ошибка',
                'error_message'     => 'Ошибка',
                'error_id'          => 'Ошибка',
                'requester_entity_id' => 'Заказчик',
                'requester_id'      => 'ID Заказчика',
            ]
        );
    }
}