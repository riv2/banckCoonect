<?php
namespace app\commands;

use app\components\base\Entity;
use app\components\DateTime;
use app\components\ReportKpiProject;
use app\models\enum\ParsingStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\PriceParsed;
use app\models\pool\ReportKpi;
use app\models\pool\ReportMatching;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use app\models\register\Error;
use app\models\register\Parsing;
use app\models\register\Task;
use Yii;
use yii\console\Controller;

class ReportController extends Controller
{
    /**
     * Проверяем, что процесс запущен
     * @param string $processName Имя процесса
     * @return bool
     * @return boolean
     */
    public function processIsRun($processName)
    {
        $result = [];
        exec("ps aux | grep -v grep | grep \"$processName\"", $result);
        $count = count($result);
        return ceil($count);
    }

    public function actionKpi($guid = null)
    {
        if ($this->processIsRun('report/kpi ') >= 2) {
            return;
        }

        $projects = Project::find()
            ->andWhere([
                'status_id' =>  Status::STATUS_ACTIVE,
            ])
            ->orderBy('name');

        if ($guid) {
            $projects->andWhere([
                'id' =>  $guid
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

    public function actionMatching(){
        if ($this->processIsRun('report/matching ') >= 1) {
            return;
        }

        /** @var Parsing[] $parsings */
        $parsings = Parsing::find()
            ->select([
                'id' => 'string_agg(id::varchar, \',\')',
                'parsing_project_id',
            ])
            ->andWhere([
                'parsing_status_id'     =>  ParsingStatus::STATUS_DONE,
                'parsing_type'          => 'collecting'
            ])
            ->groupBy(['parsing_project_id', '(CASE WHEN next_attempt_id IS NULL OR next_attempt_id = \'\' THEN parallel_main_id ELSE next_attempt_id END)'])
            ->asArray()
            ->all();

        foreach ($parsings as $parsing) {

            ReportMatching::create($parsing['id'], $parsing['parsing_project_id'], $parsing['parsing_project_id']);

//            $task = new Task;
//            $task->name = 'Отчет по сопоставлению '.$parsing->name;
//            $task->requester_id         = $parsing->id;
//            $task->requester_entity_id  = Entity::Parsing;
//            $task->task_function        = 'reportMatching';
//            $task->task_status_id       = TaskStatus::STATUS_NEW;
//            $task->task_type_id         = TaskType::TYPE_REPORT_MATCHING;
//            $task->enqueue(true);
//            sleep(1);
        }
    }
}