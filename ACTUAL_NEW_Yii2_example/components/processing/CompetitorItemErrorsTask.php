<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\register\Task;
use yii\base\BaseObject;

class CompetitorItemErrorsTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $task = new Task;
        $task->requester_entity_id  = Entity::CompetitorItem;
        $task->task_function        = 'updateErrors';
        $task->task_type_id         = TaskType::TYPE_COMPETITOR_ITEM_ERRORS;
        $task->enqueue(true);
        return true;
    }
}