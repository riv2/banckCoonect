<?php
namespace app\commands;

use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\register\FileExchange;
use app\models\register\Task;
use yii;
use yii\console\Controller;

class TaskController extends Controller
{

    /* ============== Задачи =============== */

    /**
     * Запустить задачу
     * @param $type
     * @param $id
     * @return null
     */
    public function actionRun($type, $id, $ok = false) {

        if ($type == TaskType::TYPE_PROJECT_EXECUTION_PREPARE && !$ok){
            if ($this->processIsRun('task/run '.TaskType::TYPE_PROJECT_EXECUTION_PREPARE) > 8) {
                return null;
            }
        }
        else if ($type == TaskType::TYPE_PROJECT_EXECUTION && !$ok){
            if ($this->processIsRun('task/run '.TaskType::TYPE_PROJECT_EXECUTION) > 8) {
                return null;
            }
        }
        else if ($type == TaskType::TYPE_PROJECT_EXPORT&& !$ok){
            if ($this->processIsRun('task/run '.TaskType::TYPE_PROJECT_EXPORT) > 10) {
                return null;
            }
        }
        else if ($this->processIsRun('task/run '.$type) > 4 && !$ok) {
                return null;
        }

        if ($this->processIsRun('task/run '.$type.' '.$id) > 1 && !$ok) {
            return null;
        }
        /** @var Task $task */
        $task = Task::find()
            ->andWhere([
                'id'            => $id,
                'status_id'     => Status::STATUS_ACTIVE,
            ])
            ->limit(1)
            ->one();

        if ($task) {
            $task->run();
        }
    }

    /**
     * Перейти к следующей задаче
     */
    public function actionNext() {
        Task::tryNext();
    }

    /* ============== Файловые задачи =============== */

    /**
     * Запустить обработку файла
     * @param $type
     * @param $id
     * @return null
     */
    public function actionFileExchangeRun($type, $id) {
        if ($this->processIsRun('task/file-exchange-run '.$type.' '.$id) > 2) {
            return null;
        }
        /** @var Task $task */
        $task = FileExchange::find()
            ->andWhere([
                'id'            => $id,
                'status_id'     => Status::STATUS_ACTIVE,
            ])
            ->limit(1)
            ->one();

        if ($task) {
            $task->run();
        }
    }

    /**
     * Перейти к следующей обработке файла
     * @param $type
     */
    public function actionFileExchangeNext($type = null) {
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

    /* ============== Общие функции =============== */

    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec('ps aux | grep -v grep | grep "'.$processName.'"', $result);
        $count = count($result);
        return ceil($count);
    }

    /*
     * Для тестовых нужд
     */
    public function actionTest($type) {
        $result = [];
        exec('ps aux | grep -v grep | grep "task/run '.$type.'"', $result);
        $count = count($result);
        if ($count) {
            foreach ($result as $row) {
                $row = preg_replace('/\s+/', ' ', $row);
                $process = explode(' ', $row);
                print_r($process);
                echo $process[1].PHP_EOL;
            }
            return true;
        }
        return false;
    }
}