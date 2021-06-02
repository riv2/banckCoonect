<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use yii;
use yii\helpers\Json;

/**
 * Class ExchangeSystem
 * @package app\models\enum
 *
 * @property array params
 * @property string data
 * @property string class_name
 */
class ExchangeSystem extends Reference
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
        return 'Внешние системы';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Внешние системы';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['data'], 'string'],
                [['class_name'], 'string'],
                [['params'], 'safe'],
            ]
        );
    }

    /**
     * Получение параметров
     * @return array
     */
    public function getParams() {
        $data = $this->data ? Json::decode($this->data, true): [];
        $params = array_merge(Yii::$app->params['exchange']['systems'][$this->class_name], is_array($data) ? $data : []);
        unset($params['exchangeSystem']);
        unset($params['transaction']);
        return $params;
    }

    /**
     * Установка параметров с превращением их вJSON
     * @param array $params
     */
    public function setParams($params) {
        unset($params['exchangeSystem']);
        unset($params['transaction']);
        $this->data = Json::encode($params);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $this->setParams($this->getParams());
            return true;
        }
        return false;
    }
}