<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\register\Task;
use yii\base\BaseObject;

class UpdateItemsPricesTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $tryToStart = isset($data['tryToStart']) ? true : false;
        $task = new Task;
        $task->requester_entity_id  = Entity::Item;
        $task->task_function        = 'itemUpdatePrices';
        $task->task_type_id         = TaskType::TYPE_PRICE_ORIGINS_UPDATE;
        $task->enqueue($tryToStart);
        return true;
    }
}