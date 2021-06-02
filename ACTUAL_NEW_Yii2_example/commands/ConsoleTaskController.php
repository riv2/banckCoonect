<?php

namespace app\commands;
use app\models\enum\ConsoleTaskStatus;
use app\models\enum\Status;
use app\models\reference\ConsoleTask;
use yii\console\Controller;
use yii\web\NotFoundHttpException;

class ConsoleTaskController extends Controller
{
    public function actionIndex()
    {
        $consoleTasks = ConsoleTask::find()
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'console_task_status_id' => [ConsoleTaskStatus::PLANNED, ConsoleTaskStatus::INTERRUPTED],
            ])
            ->andWhere(['<=', 'start_date', date('Y-m-d H:i:59')]);
        foreach ($consoleTasks->each() as $consoleTask) {
            var_dump($consoleTask->id);
            /** @var ConsoleTask $consoleTask */
            $consoleTask->executeAsync();
        }
    }

    public function actionRun($id)
    {
        /** @var ConsoleTask $consoleTask */
        $consoleTask = ConsoleTask::find()
            ->andWhere([
                'id' => $id,
                'status_id' => Status::STATUS_ACTIVE,
//                'console_task_status_id' => [ConsoleTaskStatus::PLANNED, ConsoleTaskStatus::INTERRUPTED]
            ])
            ->one();

        if (!$consoleTask) {
            throw new NotFoundHttpException('Задача не найдена или отключена');
        }

        $consoleTask->execute();
    }

    public function processIsRun($processName)
    {
        $result = [];
        exec('ps aux | grep -v grep | grep "'.$processName.'"', $result);
        $count = count($result);
        return ceil($count);
    }
}