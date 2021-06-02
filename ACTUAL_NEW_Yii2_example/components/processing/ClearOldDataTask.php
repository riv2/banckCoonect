<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\models\document\ProjectExecution;
use app\models\enum\Status;
use app\models\enum\TaskType;
use app\models\pool\LogKpi;
use app\models\pool\ParsingError;
use app\models\pool\PriceParsed;
use app\models\pool\PriceRefined;
use app\models\pool\ProxyParsingProject;
use app\models\pool\VpnCompetitor;
use app\models\reference\ConsoleTask;
use app\models\register\Error;
use app\models\register\FileExchange;
use app\models\register\Parsing;
use app\models\register\Task;
use yii\base\BaseObject;
use app\models\pool\ProjectChart;


class ClearOldDataTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $days = isset($consoleTask->params['days']) ? $consoleTask->params['days'] : 3;
        $date = date('Y-m-d H:i:s', strtotime("-$days days"));

        /** Запуски проектов */
        /** @var ProjectExecution[] $projectExecutions */
        $projectExecutions = ProjectExecution::find()
            ->andWhere(['<', 'created_at', $date])
            ->all();
        foreach ($projectExecutions as $projectExecution) {
            $projectExecution->delete();
        }
        echo "Cleared ProjectExecutions".PHP_EOL;

        /** Спарсенные данные */
        PriceRefined::deleteAll([
            'and', ['<', 'created_at', $date]
        ]); echo "Cleared PriceRefined".PHP_EOL;

        PriceParsed::deleteAll([
            'and', ['<', 'created_at', $date]
        ]); echo "Cleared PriceParsed".PHP_EOL;

        $parsingsIdsToDelete = Parsing::find()
            ->alias('t')
            ->select('t.id')
            ->andWhere(['<', 't.created_at', $date])
            ->andWhere(['AND', 'ppp.id IS NULL', 'vc.id IS NULL'])
            ->leftJoin(['ppp' => ProxyParsingProject::tableName()], 'ppp.parsing_id = t.id')
            ->leftJoin(['vc' => VpnCompetitor::tableName()], 'vc.parsing_id = t.id')
            ->column();
        Parsing::deleteAll([
            'and',
            ['<', 'created_at', $date],
            ['id' => $parsingsIdsToDelete],
        ]); echo "Cleared Parsing".PHP_EOL;

        LogKpi::deleteAll([
            'and', ['<', 'created_at', $date]
        ]); echo "Cleared Parsing".PHP_EOL;

        /** Задачи, файлы и т.д. */
        $dateTask = date('Y-m-d H:i:s', strtotime("-2 hours"));
        Task::deleteAll([
            'and',
            ['<', 'created_at', $dateTask],
            ['not',['status_id' => Status::STATUS_ACTIVE]],
            ['not',['task_type_id' => TaskType::TYPE_FILE_PROCESSING]]
        ]); echo "Cleared Task".PHP_EOL;

        Error::deleteAll([
            'and', ['<', 'created_at', $date]
        ]); echo "Cleared Error".PHP_EOL;

        ParsingError::deleteAll([
            'and', ['<', 'created_at', $date]
        ]); echo "Cleared ParssingError".PHP_EOL;

        FileExchange::deleteAll([
            'and', ['<', 'created_at', $date], ['or',['not',['status_id' => Status::STATUS_ACTIVE]],['had_errors' => true]]
        ]); echo "Cleared FileExchange".PHP_EOL;

        $chartsQuery = ProjectChart::find()
               ->andWhere(['<', 'created_at', $date]);

        foreach ($chartsQuery->each() as $chart) {
            $jsonData = json_decode($chart->data, true);
            foreach ($jsonData as $competitorId => $counts) {
                for($i = 0; $i < count($counts); $i++) {
                    if (!is_array($jsonData[$competitorId][$i])) {
                           continue;
                    }
                    $jsonData[$competitorId][$i] = count($jsonData[$competitorId][$i]);
                }
            }
            $chart->data = json_encode($jsonData);
            $chart->save();
       }
    }
}