<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\exchange\Exchange;
use app\models\reference\ConsoleTask;
use app\models\register\Task;
use yii\base\BaseObject;

class ProcessTasksTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        Task::tryNext();
        return true;
    }
}