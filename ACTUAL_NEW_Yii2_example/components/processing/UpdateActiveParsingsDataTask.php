<?php

namespace app\components\processing;
use app\components\base\ConsoleTaskInterface;
use app\components\DateTime;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\pool\PriceParsed;
use app\models\reference\ConsoleTask;
use app\models\register\Parsing;
use yii\base\BaseObject;
use yii\db\Query;

class UpdateActiveParsingsDataTask extends BaseObject implements ConsoleTaskInterface
{
    public static function processTask(ConsoleTask $consoleTask)
    {
        $activeParsingsIds = Parsing::find()
            ->select('id')
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING],
            ])
            ->column();

        $activeParsingsQuery = (new Query)
            ->select([
                'p.id',
                'p.parsing_project_id',
                'p.name',
                'p.global_count',
                'page_count' => '(CASE WHEN pp.page_count IS NOT NULL AND pp.page_count > 0 THEN pp.page_count ELSE pp.page_count_by_time END)',
                'pp.item_count',
                'pp.in_stock_count',
                'pe.errors_count',
            ])
            ->from(['p' => Parsing::find()->andWhere(['id' => $activeParsingsIds])])
            ->leftJoin([
                'pp' => PriceParsed::find()
                    ->select([
                        'parsing_id',
                        'COUNT(DISTINCT url) page_count',
                        'COUNT(DISTINCT extracted_at) page_count_by_time',
                        'COUNT(*) item_count',
                        'COUNT(CASE WHEN out_of_stock = false then 1 ELSE NULL END) in_stock_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->groupBy('parsing_id')
            ], 'pp.parsing_id = p.id')
            ->leftJoin([
                'pe' => ParsingError::find()
                    ->select([
                        'parsing_id',
                        'COUNT(*) as errors_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->groupBy('parsing_id')
            ], 'pe.parsing_id = p.id')
            ->groupBy([
                'p.id', 'p.parsing_project_id', 'p.name', 'p.global_count',
                'pp.page_count', 'pp.page_count_by_time', 'pp.item_count',
                'pp.in_stock_count', 'pe.errors_count'
            ])
            ->orderBy('p.name');

        \Yii::$app->cache->set('active_parsings_data', $activeParsingsQuery->all());

        $query = Parsing::find()
            ->alias('p')
            ->select([
                '*',
                'success' => '(CASE WHEN ppa.items_count IS NOT NULL AND ppa.items_count > 0 THEN ppa.items_count ELSE ppa.items_count_by_time END)',
                'errors' => 'pe.errors_count',
                'all_parsed' => 'ppac.items_count',
                'all_errors' => 'pec.errors_count',
                'last_count' => '(p.global_count - (CASE WHEN ppac.items_count IS NOT NULL AND ppac.items_count > 0 THEN ppac.items_count ELSE ppac.items_count_by_time END) - (CASE WHEN pec.errors_count IS NOT NULL THEN pec.errors_count ELSE 0 END))'
            ])
            ->from([
                'p' => Parsing::find()
                    ->select([
                        'id',
                        'parsing_project_id',
                        'name',
                        'created_at',
                        'global_count'// => '(global_count - ppac.items_count)'
                    ])
                    ->andWhere(['id' => $activeParsingsIds])
            ])
            ->leftJoin([
                'ppa' => PriceParsed::find()
                    ->select([
                        'parsing_id',
                        'COUNT(DISTINCT url) items_count',
                        'COUNT(DISTINCT extracted_at) items_count_by_time'
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->andWhere(['>', 'extracted_at', date('Y-m-d H:i:s', strtotime('5 minutes ago'))])
                    ->groupBy('parsing_id')
            ], 'ppa.parsing_id = p.id')
            ->leftJoin([
                'ppac' => PriceParsed::find()
                    ->select([
                        'parsing_id',
                        'COUNT(DISTINCT url) items_count',
                        'COUNT(DISTINCT extracted_at) items_count_by_time'
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->groupBy('parsing_id')
            ], 'ppac.parsing_id = p.id')
            ->leftJoin([
                'pe' => ParsingError::find()
                    ->select([
                        'parsing_id',
                        'COUNT(*) as errors_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->andWhere(['>', 'created_at', date('Y-m-d H:i:s', strtotime('5 minutes ago'))])
                    ->groupBy('parsing_id')
            ], 'pe.parsing_id = p.id')
            ->leftJoin([
                'pec' => ParsingError::find()
                    ->select([
                        'parsing_id',
                        'COUNT(*) as errors_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->groupBy('parsing_id')
            ], 'pec.parsing_id = p.id')
            ->orderBy('p.created_at, p.name')
            ->asArray();

        $timeData = [];

        /** @var Parsing $parsing */
        foreach ($query->each() as $parsing) {
            $date = (new DateTime())->setTimestamp(time());
            $speed = $parsing['success'];
            if (!$speed || $speed <= 0) {
                continue;
            }
            $interval = $parsing['last_count'] / $speed;
            if ($interval <= 0) {
                continue;
            }
            $endTime = $date->add(new \DateInterval('PT' . round($interval) * 5 . 'M'));
            $timeData[$parsing['id']] = $endTime->getTimestamp();
        }

        \Yii::$app->cache->set('active_parsings_data_times', $timeData);
    }
}