<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\DateTime;
use app\components\ValidationRules;
use yii\helpers\Html;

/**
 * Модель vpn-сервера
 *
 * @property string id
 * @property string provider
 * @property string username
 * @property string password
 * @property string $config
 * @property DateTime $until
 */
class Vpn extends Reference
{
    /**
     * @inheritdoc
     */
    public function getSingularName()
    {
        return 'VPN-сервер';
    }

    /**
     * @inheritdoc
     */
    public function getPluralName()
    {
        return 'VPN-сервера';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'provider'  => 'Провайдер',
            'username'  => 'Логин',
            'password'  => 'Пароль',
            'config'    => 'Конфигурация',
            'until'     => 'Действителен до',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return [
            'name' => [
                'label' => 'Наименование',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(Html::decode((string)$model), ['update', 'id' => $model->id], ['data-pjax' => '0']);
                }
            ],
            'until' => [
                'attribute' => 'until',
                'format' => 'datetime',
                'value' => function ($model) {
                    /** @var Vpn $model */
                    return $model->until ? $model->until->format('Y-m-d') : null;
                },
                'contentOptions' => function ($model) {
                    /** @var Vpn $model */
                    if (!$model->until) {
                        return [];
                    }
                    $timestamp = $model->until->getTimestamp();
                    if ($timestamp < time()) {
                        return ['style' => 'background-color:#ff00005c;'];
                    } else if ($timestamp < strtotime('7 days')) {
                        return ['style' => 'background-color:#ffff005c;'];
                    }
                    return [];
//                    if ($model->until)
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid(['id']),
            ValidationRules::ruleRequired(['provider', 'config']),
            ValidationRules::ruleDateTime('until'),
            [
                [['name', 'provider', 'username', 'password'], 'string', 'max' => 32],
                [['provider'], 'in', 'range' => array_keys(self::getProviders())]
            ]
        );
    }

    /**
     * Провайдеры VPN
     * @return array
     */
    public static function getProviders()
    {
        return [
            '' => '(не указано)',
            'openvpn' => 'OpenVPN',
            'hma' => 'HideMyAss',
        ];
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->name ?: self::getProviders()[$this->provider] . ' ' . $this->id;
    }
}