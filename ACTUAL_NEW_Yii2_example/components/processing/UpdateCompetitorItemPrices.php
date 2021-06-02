<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\register\Task;
use yii\base\BaseObject;

class UpdateCompetitorItemPrices extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $task = new Task;
        $task->requester_id         = null;
        $task->requester_entity_id  = Entity::CompetitorItem;
        $task->task_function        = 'updatePrices';
        $task->task_status_id       = TaskStatus::STATUS_RUNNING;
        $task->task_type_id         = TaskType::TYPE_COMPETITOR_ITEM_UPDATE_PRICES;
        $task->enqueue(true);
    }
}