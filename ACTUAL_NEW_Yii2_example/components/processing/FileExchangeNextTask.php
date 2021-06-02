<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\reference\ConsoleTask;
use app\models\register\FileExchange;
use app\models\register\Task;
use yii\base\BaseObject;

class FileExchangeNextTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $type = null;
        if ($type == 'export') {
            $type = [true];
        } else if ($type == 'import') {
            $type = [false];
        } else {
            $type = [false,true];
        }
        foreach ($type as $t) {
            /** @var Task[] $tasks */
            $tasks = FileExchange::find()
                ->andWhere([
                    'task_status_id' => TaskStatus::STATUS_QUEUED,
                    'is_export' => $t,
                    'status_id' => Status::STATUS_ACTIVE,
                ])
                ->orderBy([
                    'created_at' => SORT_ASC
                ])
                ->all();
            if ($tasks) {
                foreach ($tasks as $task) {
                    $task->tryToRun();
                }
            }
        }
    }

}