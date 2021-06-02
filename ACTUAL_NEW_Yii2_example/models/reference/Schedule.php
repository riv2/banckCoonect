<?php
namespace app\models\reference;

use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\base\ScheduleTrait;
use app\components\base\type\Reference;
use app\components\DateTime;
use app\components\ValidationRules;
use yii\helpers\Json;

/**
 * Class Schedule
 * @package app\models\reference
 * 
 * @property string     time
 * @property int        day
 * @property int        requester_entity_id
 * @property string     requester_id
 * @property string     description
 * @property string     duration
 * @property bool       started
 *
 * 
 * @property string     args
 * @property array      params
 * @property BaseModel|ScheduleTrait  requester
 * 
 */
class Schedule extends Reference
{

    protected $_requester = null;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Расписание';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Расписание';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleUuid('requester_id'),
            ValidationRules::ruleRequired(['requester_entity_id','day','time']),
            [
                [['day'], 'number', 'integerOnly' => true],
                [['time', 'duration'], 'date', 'format' => 'php:' . DateTime::DB_TIME_FORMAT],
                [['description', 'function', 'args'], 'string'],
                [['started'], 'boolean'],
            ],
            ValidationRules::ruleEnum('requester_entity_id', Entity::className())
        );
    }

    public function setupBasedOnRequester() {
        $model = $this->requester;
        if ($model) {
            if (!$this->name) {
                $this->name = $model->getScheduleTitle($this->params);
            }
            if (!$this->description) {
                $this->description = $model->getScheduleDescription($this->params);
            }
            if (!$this->duration) {
                $this->duration = $model->getScheduleDuration($this->params);
            }
        }
    }

    public function beforeValidate()
    {
        $this->setupBasedOnRequester();
        return parent::beforeValidate();
    }

    public function load($data, $formName = null)
    {
        $load = parent::load($data, $formName);
        $this->setupBasedOnRequester();
        return $load;
    }

    /**
     * @return array|mixed
     */
    public function getParams() {
        if (!$this->args) {
            return [];
        }
        return Json::decode($this->args, true);
    }

    /**
     * @param $val
     */
    public function setParams($val) {
        $this->args = Json::encode($val);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequesterEntity() {
        return $this->hasOne(Entity::className(), ['id' => 'requester_entity_id']);
    }

    /**
     * @return BaseModel
     */
    public function getRequester() {
        if ($this->_requester) {
            return $this->_requester;
        }
        if ($this->requester_entity_id && $this->requester_id) {
            $requesterClass = Entity::getClassNameById($this->requester_entity_id);
            if ($requesterClass) {
                $requester = $requesterClass::findOne($this->requester_id);
                if ($requester) {
                    $this->_requester = $requester;
                    return $requester;
                }
            }
        }
        return null;
    }


}