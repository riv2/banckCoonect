<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\reference\ParsingProject;
use app\models\reference\Schedule;
use app\models\register\Task;
use yii\base\BaseObject;
use yii\base\UserException;

class ParsingProjectExecutionTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $dayOfWeek = intval(date("N"),10);

        $dayOfWeek++;

        if ($dayOfWeek == 8) {
            $dayOfWeek = 1;
        }

        /** @var Schedule[] $schedules */
        $schedules = Schedule::find()
            ->andWhere([
                'day' => $dayOfWeek,
                'requester_entity_id' => Entity::ParsingProject,
                'started' => false,
            ])
            ->andWhere(['<', 'time', date('H:i:s')])
            ->andWhere('CASE WHEN created_at::date = NOW()::date THEN time > created_at::time ELSE true END')
            ->all();

        foreach ($schedules as $item) {
            echo $item.PHP_EOL;
            /** @var ParsingProject $requester */
            $requester = $item->requester;
            if ($requester) {
                echo 'start: ' . $requester->id . ' (' . $item->time . ')' . PHP_EOL;
                $task = new Task;
                $task->requester_id         = $requester->id;
                $task->requester_entity_id  = Entity::ParsingProject;
                $task->task_function        = 'startParsing';
                $task->task_type_id         = TaskType::TYPE_START_PARSING;
                $task->enqueue(true);
                $item->started = true;
                $item->save();
            }
        }
        Schedule::updateAll(
            ['started' => false],
            [
                'OR',
                ['!=', 'day', $dayOfWeek],
                ['>', 'time', date('H:i:s')]
            ]
        );
        return true;
    }
}