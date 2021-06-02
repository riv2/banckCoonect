<?php

namespace app\models\reference;

use app\components\base\ConsoleTaskInterface;
use app\components\base\type\Reference;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\ConsoleTaskStatus;
use app\models\enum\ConsoleTaskType;
use app\models\register\Error;
use yii\helpers\Json;

/**
 * Модель задачи, запускаемой cron'ом
 *
 * Свойства:
 * @property integer  $console_task_type_id         тип задачи
 * @property integer  $console_task_status_id       статус задачи
 * @property string   $params          параметры задачи (json)
 * @property DateTime $start_date      время ближайшего запуска
 * @property DateTime $finish_date     дата последнего завершения
 * @property boolean  $is_repeatable   флаг повторяемой задачи
 * @property string   $repeat_interval период повторения
 * @property string   $result_text     результат (отображаемый)
 * @property string   $result_data     данные результата (не отображаемые) (json)
 *
 * Отношения:
 * @property ConsoleTaskType   $consoleTaskType
 * @property ConsoleTaskStatus $consoleTaskStatus
 */
class ConsoleTask extends Reference
{
    public static function getSingularNominativeName()
    {
        return 'Консольная команда';
    }

    public static function getPluralNominativeName()
    {
        return 'Консольные команды';
    }

    public function init()
    {
        parent::init();
        if ($this->scenario === self::SCENARIO_CREATE && $this->isNewRecord) {
            $this->start_date = date('Y-m-d H:i:00');
        }
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'consoleTaskType'   => 'Тип задачи',
            'consoleTaskStatus' => 'Статус задачи',
            'params'            => 'Параметры',
            'start_date'        => 'Время следующего запуска',
            'finish_date'       => 'Дата последнего завершения',
            'result_data'       => 'Данные результата',
            'result_text'       => 'Результат',
            'repeat_interval'   => 'Период повторения (в минутах)',
            'is_repeatable'     => 'Повторять',
        ]);
    }

    public function rules()
    {
        return array_merge(parent::rules(),
            ValidationRules::ruleDateTime(['start_date', 'finish_date']),
            [
                [['console_task_type_id', 'console_task_status_id'], 'integer'],
                [['result_text', 'result_data', 'params'], 'string'],
                [['is_repeatable'], 'boolean'],
                [['repeat_interval'], 'safe'],
            ]
        );
    }

    public function beforeValidate()
    {
        if ($this->isNewRecord) {
            $this->id = static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar();
        }
        if (is_array($this->result_data)) {
            $this->result_data = Json::encode($this->result_data);
        }
        $this->setNewStartDate();
        return parent::beforeValidate();
    }

    public function afterFind()
    {
        $this->repeat_interval = Json::decode($this->repeat_interval);
        parent::afterFind();
    }

    public function executeAsync()
    {
        exec('php /app/yii console-task/run ' . $this->id . ' > /dev/null 2>&1 &');
    }

    public function execute()
    {
        /** @var ConsoleTaskInterface $taskClass */
        $taskClass = (ConsoleTaskType::getTaskProcessorClassByTypeId($this->console_task_type_id));
        $this->console_task_status_id = ConsoleTaskStatus::IN_PROGRESS;
        $this->save();
        $transaction = \Yii::$app->db->beginTransaction();
        try {

            ob_start();
            $this->result_data = Json::encode($taskClass::processTask($this));
            $this->result_text = ob_get_flush();

            $this->console_task_status_id = ConsoleTaskStatus::FINISHED;
            $this->finish_date = new DateTime();
            if ($this->is_repeatable && $this->repeat_interval) {
                $this->setNewStartDate();
                $this->console_task_status_id = ConsoleTaskStatus::PLANNED;
            }
            $this->save();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Error::logError($ex);
            $this->result_text = 'Error: ' . $ex->getMessage();
            $this->result_data = Json::encode([
                'message' => $ex->getMessage(),
                'trace' => $ex->getTraceAsString(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
            ]);
            $this->finish_date = new DateTime();
            $this->console_task_status_id = ConsoleTaskStatus::INTERRUPTED;
            if ($this->is_repeatable && $this->repeat_interval) {
                $this->setNewStartDate();
                $this->console_task_status_id = ConsoleTaskStatus::PLANNED;
            }
            $this->save();
        }
    }

    public function setNewStartDate()
    {
        if ($this->is_repeatable && $this->repeat_interval) {
            $this->start_date = $this->getNewStartDate();
        }
    }

    public static function getIntervalFromMinutes($minutes, ConsoleTask $consoleTask = null)
    {
        if ($minutes > 30) {
            if ($minutes > 720) {
                $repeatInterval = [
                    'dayOfMonth' => range(0, 31, min(31, round($minutes / 60 / 24))),
                    'hourOfDay' => [$consoleTask ? $consoleTask->start_date->format('H') : 0],
                    'minuteOfHour' => [$consoleTask ? $consoleTask->start_date->format('i') : 0],
                ];
            } else {
                $repeatInterval = [
                    'hourOfDay' => range(0, 23, round($minutes / 60)),
                    'minuteOfHour' => [$consoleTask ? $consoleTask->start_date->format('i') : 0],
                ];
            }
        } else {
            $repeatInterval = [
                'minuteOfHour' => range(0, 59, max(1, $minutes))
            ];
        }
        return $repeatInterval;
    }

    public function getNewStartDate($curDate = null)
    {
        $repeatInterval = self::getIntervalFromMinutes($this->repeat_interval, $this);
        if (is_null($curDate)) {
            $curDate = new DateTime();
        }
        if (empty($repeatInterval['monthOfYear'])) {
            $repeatInterval['monthOfYear'] = range(1, 12);
        } else {
            sort($repeatInterval['monthOfYear']);
        }
        if (empty($repeatInterval['dayOfMonth'])) {
            $repeatInterval['dayOfMonth'] = range(1, 31);
        } else {
            sort($repeatInterval['dayOfMonth']);
        }
        if (empty($repeatInterval['dayOfWeek'])) {
            $repeatInterval['dayOfWeek'] = range(1, 7);
        }
        if (!isset($repeatInterval['hourOfDay']) || $repeatInterval['hourOfDay'] === '' || $repeatInterval['hourOfDay'] === null || $repeatInterval['hourOfDay'] === []) {
            $repeatInterval['hourOfDay'] = range(0, 23);
        } else {
            sort($repeatInterval['hourOfDay']);
        }
        if (!isset($repeatInterval['minuteOfHour']) || $repeatInterval['minuteOfHour'] === '' || $repeatInterval['minuteOfHour'] === null || $repeatInterval['minuteOfHour'] === []) {
            $repeatInterval['minuteOfHour'] = range(0, 59);
        } else {
            sort($repeatInterval['minuteOfHour']);
        }
        $curYear = (integer)$curDate->format('Y');
        $years = [$curYear, $curYear + 1];
        foreach ($years as $year) {
            foreach ($repeatInterval['monthOfYear'] as $month) {
                foreach ($repeatInterval['dayOfMonth'] as $day) {
                    $newDateTime = new DateTime();
                    $newDateTime->setDate($year, $month, $day);
                    if ($day != $newDateTime->format('j')) {
                        continue;
                    }
                    if (!in_array($newDateTime->format('N'), $repeatInterval['dayOfWeek'])) {
                        continue;
                    }
                    foreach ($repeatInterval['hourOfDay'] as $hour) {
                        foreach ($repeatInterval['minuteOfHour'] as $minute) {
                            $newDateTime->setTime($hour, $minute);
                            if ((string)$newDateTime > (string)$curDate) {
                                return $newDateTime;
                            }
                        }
                    }
                }
            }
        }
        return null;
    }

    public static function relations()
    {
        return array_merge(parent::relations(), [
            'consoleTaskType',
            'consoleTaskStatus',
        ]);
    }

    public function crudIndexColumns()
    {
        return [
            'name',
            'consoleTaskStatus',
            'start_date',
            'finish_date',
            'repeat_interval',
        ];
    }

    public function getConsoleTaskType()
    {
        return $this->hasOne(ConsoleTaskType::className(), ['id' => 'console_task_type_id']);
    }

    public function getConsoleTaskStatus()
    {
        return $this->hasOne(ConsoleTaskStatus::className(), ['id' => 'console_task_status_id']);
    }
}