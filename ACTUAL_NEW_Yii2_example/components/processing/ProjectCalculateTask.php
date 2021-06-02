<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\enum\Status;
use app\models\enum\TaskType;
use app\models\reference\ConsoleTask;
use app\models\reference\Project;
use app\models\reference\Schedule;
use yii\base\BaseObject;

class ProjectCalculateTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $dayOfWeek = intval(date("N"),10);

        $dayOfWeek++;

        if ($dayOfWeek == 8) {
            $dayOfWeek = 1;
        }


        /** @var Project[] $projects */
        $projects = Project::find()
            ->andWhere([
                'status_id'            => Status::STATUS_ACTIVE,
                'schedule_started'     => false,
            ])
            ->andWhere([
                'LIKE',
                'scheduled_weekdays',
                '%'.$dayOfWeek.'%',
                false
            ])
            ->andWhere(['<=', 'scheduled_daily_time', date('H:i:59')])
            ->andWhere('CASE WHEN created_at::date = NOW()::date THEN scheduled_daily_time > created_at::time ELSE true END')
            ->all();

        foreach ($projects as $project) {
            echo $project->name . PHP_EOL;
            $project->prepareProjectExecution(false);
            $project->schedule_started = true;
            $project->save();
        }
        Project::updateAll(
            ['schedule_started' => false],
            [
                'OR',
//                ['LIKE', 'scheduled_weekdays', '%'.$dayOfWeek.'%', false],
                ['>', 'scheduled_daily_time', date('H:i:s')]
            ]
        );
        return true;
    }

    public static function processIsRun($processName)
    {
        $result = [];
        exec('ps aux | grep -v grep | grep "'.$processName.'"', $result);
        $count = count($result);
        return ceil($count);
    }
}