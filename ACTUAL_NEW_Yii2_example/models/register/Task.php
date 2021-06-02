<?php
namespace app\models\register;

use app\components\DateTime;
use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\base\type\Register;
use app\components\ValidationRules;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use yii;
use yii\helpers\Json;

/**
 * Процесс
 *
 * Class Task
 * @package app\models\reference
 *
 * @property string     name                  => 'Название',
 * @property int        task_type_id          => 'Тип',
 * @property int        task_status_id        => 'Статус',
 * @property int        requester_entity_id   => 'Сущности',
 * @property string     requester_id          => 'ID сущности',
 * @property int        priority              => 'Приоритет',
 * @property string     task_function         => 'Функция',
 * @property string     task_params           => 'Параметры',
 * @property int        total                 => 'Всего',
 * @property int        progress              => 'Обработано',
 * @property int        errors                => 'Ошибок',
 * @property bool       had_errors            => 'Были ошибки',
 * @property bool       is_external
 * @property DateTime   started_at            => 'Начат в',
 * @property DateTime   finished_at           => 'Завершен в',
 * @property string     result_text
 *
 * @property TaskType   taskType
 * @property TaskStatus taskStatus
 * @property Entity     requesterEntity
 * @property BaseModel     requester
 */

class Task extends Register
{
    const WS_CHANNEL = 'common';
    const WS_UPDATE_EVENT = 'taskUpdated';

    const WS_ACTION_PROGRESS = 'progress';
    const WS_ACTION_FINISHED = 'finished';
    const WS_ACTION_CANCELED = 'canceled';
    const WS_ACTION_STARTED  = 'started';
    const WS_ACTION_FAILED   = 'failed';

    protected $_requester = null;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Процесс';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Процессы';
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            [
                [['name', 'task_function', 'task_params','requester_id', 'result_text'], 'string'],
                [['had_errors','is_external'], 'boolean'],
                [['progress','errors','total','priority','requester_entity_id'], 'number', 'integerOnly' => true],
            ],
            ValidationRules::ruleDateTime('started_at'),
            ValidationRules::ruleDateTime('finished_at'),
            ValidationRules::ruleDefault('priority', 0),
            ValidationRules::ruleDefault(['progress','errors','total'], 0),
            ValidationRules::ruleDefault('had_errors', false),
            ValidationRules::ruleDefault('is_external', false),
            ValidationRules::ruleDefault('task_type_id', TaskType::TYPE_COMMON),
            ValidationRules::ruleDefault('task_status_id', TaskStatus::STATUS_NEW),
            ValidationRules::ruleEnum('task_type_id', TaskType::className()),
            ValidationRules::ruleEnum('task_status_id', TaskStatus::className())
        );
    }

    /**
     * Выполнить задачу немедленно
     * @throws yii\db\IntegrityException
     */
    public function run() {
        $method = 'task'.strtoupper(substr($this->task_function, 0,1)).substr($this->task_function, 1) ;

        if ($this->requester_entity_id && $this->requester_id) {
            if (!$this->requester) {
                throw new yii\base\InvalidValueException("Для процесса не найдена запись {$this->requester_id} класса ".$this->requester->className());
            }
            if (is_callable([$this->requester, $method])) {
                $this->requester->$method($this);
            } else if (is_callable([$this->requester, 'taskOne'])){
                $method = 'taskOne';
                $this->requester->$method($this);
            } else {
                throw new yii\base\InvalidCallException("Метод $method отсутствует в классе ".$this->requester->className());
            }
        } else if($this->requester_entity_id) {
            $requesterClass = Entity::getClassNameById($this->requester_entity_id);
            if ($requesterClass) {
                if (is_callable($requesterClass, $method)) {
                    call_user_func([$requesterClass, $method], $this);
                } else if (is_callable([$this->requester, 'taskGlobal'])){
                    call_user_func([$requesterClass, 'taskGlobal'], $this);
                } else {
                    throw new yii\base\InvalidCallException("Метод $method отсутствует в классе ".$requesterClass);
                }
            } else {
                throw new yii\base\InvalidValueException("Не найдена сущность с ID = ".$this->requester_entity_id);
            }
        }
    }

    /**
     * Попытаться запустить задачу, если не ограничен запуск по её типу
     */
    public function tryToRun() {
        shell_exec('php '.Yii::getAlias('@app').'/yii task/run ' . $this->task_type_id .' '. $this->id.' > /dev/null 2>/dev/null &');
    }

    public static function tryNext($type = null) {
        /** @var Task[] $tasks */
        $find = Task::find()
            ->andWhere([
                'task_status_id'    => TaskStatus::STATUS_QUEUED,
                'status_id'         => Status::STATUS_ACTIVE,
                'is_external'       => false,
            ])
            ->orderBy([
                'created_at'        => SORT_ASC
            ]);

        if ($type) {
            $find->andWhere([
                'task_type_id'      => $type
            ]);
        }

        $tasks = $find->all();

        if ($tasks) {
            foreach ($tasks as $task) {
                $task->tryToRun();
                sleep(1);
            }
        }
    }

    /**
     * Убить задачу
     */
    public function cancel() {
        $result = [];
        if (!$this->is_external) {
            exec('ps aux | grep -v grep | grep "task/run ' . $this->task_type_id . ' ' . $this->id . '"', $result);
            $count = count($result);
            if ($count) {
                foreach ($result as $row) {
                    $row = preg_replace('/\s+/', ' ', $row);
                    $process = explode(' ', $row);
                    shell_exec('kill -9 ' . $process[1]);
                }
            }

        }
        $this->status_id = Status::STATUS_DISABLED;
        $this->task_status_id = TaskStatus::STATUS_CANCELED;
        $this->afterCancel();
        $this->save();
        return true;
    }

    /**
     * Прибраться после убийства задачи
     * @throws yii\db\IntegrityException
     */
    public function afterCancel() {
        $method = ($this->task_function) ? 'taskCancel'.strtoupper(substr($this->task_function, 0,1)).substr($this->task_function, 1) : 'taskCancel';
        if ($this->requester_entity_id && $this->requester_id) {
            if (!$this->requester) {
                //throw new yii\base\InvalidValueException("Для процесса не найдена запись {$this->requester_id} класса ".$this->requester->className());
            }
            if (is_callable([$this->requester, $method]) && method_exists($this->requester, $method)) {
                $this->requester->$method($this);
            } else if (is_callable([$this->requester, 'taskCancelOne']) && method_exists($this->requester, 'taskCancelOne')){
                $method = 'taskCancelOne';
                $this->requester->$method($this);
            } else {
                //throw new yii\base\InvalidCallException("Метод $method отсутствует в классе ".$this->requester->className());
            }
        } else if($this->requester_entity_id) {
            $requesterClass = Entity::getClassNameById($this->requester_entity_id);
            if ($requesterClass) {
                if (is_callable($requesterClass, $method) && method_exists($requesterClass, $method)) {
                    call_user_func([$requesterClass, $method], $this);
                } else if (is_callable([$requesterClass, 'taskCancelGlobal']) && method_exists($requesterClass, 'taskCancelGlobal')){
                    call_user_func([$requesterClass, 'taskCancelGlobal'], $this);
                } else {
                    //throw new yii\base\InvalidCallException("Метод $method отсутствует в классе ".$requesterClass);
                }
            } else {
                //throw new yii\base\InvalidValueException("Не найдена сущность с ID = ".$this->requester_entity_id);
            }
        }
    }

    /**
     * Сброс кеша статистики
     */
    public static function resetCache() {
        Yii::$app->cache->delete("#tasks#");
    }

    /**
     * Поставить в очередь и попытаться запустить
     * @param $tryToStartNow
     */
    public function enqueue($tryToStartNow = true) {
        if (!$this->id) {
            $this->id = self::getDb()->createCommand("SELECT uuid_generate_v4();")->queryScalar();
        }
        if (!$this->task_status_id || $this->task_status_id == TaskStatus::STATUS_NEW ) {
            $this->task_status_id = TaskStatus::STATUS_QUEUED;
        }
        $this->save();
        if ($tryToStartNow) {
            $this->tryToRun();
        }
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $action = static::WS_ACTION_PROGRESS;
        if (isset($changedAttributes['task_status_id'])) {
            if ($this->task_status_id == TaskStatus::STATUS_FAILED) {
                $action = static::WS_ACTION_FAILED;
            } else if ($this->task_status_id  == TaskStatus::STATUS_FINISHED) {
                $action = static::WS_ACTION_FINISHED;
            } else if ($this->task_status_id  == TaskStatus::STATUS_CANCELED) {
                $action = static::WS_ACTION_CANCELED;
            } else if ($changedAttributes['task_status_id'] == TaskStatus::STATUS_QUEUED) {
                $action = static::WS_ACTION_STARTED;
            }
        }
        Yii::$app->ws->publishEvent(static::WS_CHANNEL, static::WS_UPDATE_EVENT, [
            'Task'      => $this->toArray(),
            'action'    => $action
        ]);
//        if (in_array($this->task_status_id, [TaskStatus::STATUS_FINISHED, TaskStatus::STATUS_FAILED, TaskStatus::STATUS_CANCELED])) {
//            $function = yii\helpers\Inflector::camel2id($this->task_function);
//            shell_exec('php ' . Yii::getAlias('@app') . '/yii task/' . $function . ' > /dev/null 2>/dev/null &');
//        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name'                  => 'Название',
            'task_type_id'          => 'Тип',
            'task_status_id'        => 'Статус',
            'requester_entity_id'   => 'Сущности',
            'requester_id'          => 'ID сущности',
            'priority'              => 'Приоритет',
            'task_function'         => 'Функция',
            'task_params'           => 'Параметры',
            'total'                 => 'Всего',
            'progress'              => 'Обработано',
            'errors'                => 'Ошибок',
            'had_errors'            => 'Были ошибки',
            'started_at'            => 'Начат в',
            'finished_at'           => 'Завершен в',
            'result_text'           => 'Текст результата',
        ]);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        static::resetCache();
        if (parent::beforeSave($insert)) {
            if (!$this->name && $this->scenario != self::SCENARIO_SEARCH) {
                $this->name = TaskType::getNameById($this->task_type_id);
                $requester = $this->requester;
                if ($requester) {
                    $this->name = "$requester";
                }
            }
            if ($this->status_id != Status::STATUS_REMOVED) {
                if (in_array($this->task_status_id, [TaskStatus::STATUS_FINISHED, TaskStatus::STATUS_CANCELED, TaskStatus::STATUS_FAILED])) {
                    $this->status_id = Status::STATUS_DISABLED;
                } else {
                    $this->status_id = Status::STATUS_ACTIVE;
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @return array|mixed
     */
    public function getParams() {
        if (!$this->task_params)
            return [];
        return Json::decode($this->task_params);
    }

    /**
     * @param $array
     */
    public function setParams($array) {
        $this->task_params     = Json::encode($array);
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(parent::relations(),[
            'taskType',
            'taskStatus',
            'requesterEntity',
        ]);
    }

    public function crudIndexColumns()
    {
        return array_merge(parent::crudIndexColumns(), [
            'name',
            'taskType',
            'taskStatus',
            'created_at',
            'updated_at',
            'started_at',
            'finished_at',
            'status',
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskType() {
        return $this->hasOne(TaskType::className(), ['id' => 'task_type_id'] );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTaskStatus() {
        return $this->hasOne(TaskStatus::className(), ['id' => 'task_status_id'] );
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

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return $this->name?:parent::__toString();
    }

}