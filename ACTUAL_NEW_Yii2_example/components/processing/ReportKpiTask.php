<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\base\Entity;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\reference\Project;
use app\models\register\Task;
use yii\base\BaseObject;

class ReportKpiTask extends BaseObject implements ConsoleTaskInterface
{
    const MAX_THREADS_COUNT_DEFAULT = 8;

    public static function processTask(ConsoleTask $consoleTask)
    {
        $guids = isset($consoleTask->params['guids']) ? $consoleTask->params['guids'] : null;
        $projects = Project::find()
            ->andWhere([
                'status_id' =>  Status::STATUS_ACTIVE,
            ])
            ->orderBy('name');

        if ($guids) {
            $projects->andWhere([
                'id' =>  $guids
            ]);
        }
        /** @var Project[] $projects */
        $projects = $projects->all();

        foreach ($projects as $id => $project) {
            echo $project->id.' ';
            $task = new Task;
            $task->name = 'Расчет KPI '.$project->name;
            $task->requester_id         = $project->id;
            $task->requester_entity_id  = Entity::Project;
            $task->task_function        = 'reportKpi';
            $task->task_status_id       = TaskStatus::STATUS_NEW;
            $task->task_type_id         = TaskType::TYPE_REPORT_KPI;
            $task->enqueue(true);
            sleep(1);
        }
    }

}