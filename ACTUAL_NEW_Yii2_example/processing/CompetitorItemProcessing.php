<?php
namespace app\processing;

use app\components\base\Entity;
use app\components\DateTime;
use app\models\enum\ErrorType;
use app\models\enum\ParsingStatus;
use app\models\enum\Region;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\ParsingError;
use app\models\pool\PriceRefined;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use app\models\reference\ProjectCompetitor;
use app\models\reference\ProjectItem;
use app\models\register\Error;
use app\models\register\Parsing;
use app\models\register\Task;
use yii\db\Expression;

class CompetitorItemProcessing
{
    /**
     * Актуализация цен и урлов у конкурентов
     * @param Task $task
     */
    public static function updatePrices(Task $task) {

        $activeProjects = Project::find()
            ->andWhere(['status_id' => Status::STATUS_ACTIVE])
            ->select('id')
            ->column();
        $activeCompetitors  = ProjectCompetitor::find()
            ->andWhere([
                'project_id' => $activeProjects,
                'status_id' => Status::STATUS_ACTIVE,
            ])
            ->select('competitor_id')
            ->groupBy('competitor_id')
            ->column();


        $task->started_at           = new DateTime();
        $task->task_status_id = TaskStatus::STATUS_RUNNING;
        $task->save();
        $task->progress             = 0;

        $task->total                = CompetitorItem::find()
            ->andWhere([
                'competitor_id' => $activeCompetitors,
                'status_id' => Status::STATUS_ACTIVE,
            ])
            ->andWhere(['or',
                [
                    '<', 'price_updated_at', new DateTime('-23 hour')
                ],
                [
                    'price_updated_at' => null
                ]
            ])
            ->count();
        $task->task_status_id = TaskStatus::STATUS_RUNNING;
        $task->save();

        try {
            foreach (CompetitorItem::find()
                         ->andWhere([
                             'competitor_id' => $activeCompetitors,
                             'status_id' => Status::STATUS_ACTIVE,
                         ])
                         ->andWhere(['or',
                             [
                                 '<', 'price_updated_at', new DateTime('-23 hour')
                             ],
                             [
                                 'price_updated_at' => null
                             ]
                         ])
                         ->orderBy(['price_updated_at' => SORT_ASC])
                         ->select(['id', 'item_id', 'competitor_id', 'price','competitor_item_name','sku'])
                         ->asArray()
                         ->batch(1000) as $items) {

                foreach ($items as $item) {
                    $priceRefined = PriceRefined::find()
                        ->andWhere([
                            'source_id' => Source::SOURCE_WEBSITE,
                            'item_id' => $item['item_id'],
                            'competitor_id' => $item['competitor_id'],
                            'competitor_item_sku' => $item['sku'],
                            'out_of_stock' => false,
                        ])
                        ->andWhere(new Expression("regions @> '".Region::DEFAULT_REGION."'"))
                        ->orderBy([
                            'extracted_at' => SORT_DESC
                        ])
                        ->asArray()
                        ->select(['price', 'competitor_item_name', 'competitor_item_sku'])
                        ->one();

                    $needUpdate = false;
                    if (!empty($priceRefined['price']) && $priceRefined['price'] != $item['price']) {
                        $needUpdate = true;
                    }
                    if (!empty($priceRefined['competitor_item_name']) && $priceRefined['competitor_item_name'] != $item['competitor_item_name']) {
                        $needUpdate = true;
                    }
                    if ($needUpdate) {
                        CompetitorItem::updateAll([
                            'price' => empty($priceRefined['price']) ? null : $priceRefined['price'],
                            'competitor_item_name' => empty(trim($priceRefined['competitor_item_name'])) ? null : $priceRefined['competitor_item_name'],
                            'price_updated_at' => new DateTime(),
                            'sku' => $priceRefined['competitor_item_sku']
                        ], [
                            'id' => $item['id'],
                        ]);
                    }
                }
                $task->progress += count($items);
                $task->save();
            }
            $task->finished_at          = new DateTime();
            $task->task_status_id       = TaskStatus::STATUS_FINISHED;
            $task->save();
        } catch (\Error $e) {
            $task->task_status_id       = TaskStatus::STATUS_FAILED;
            $task->save();
        }
    }

    /**
     * @param Task $task
     */
    public static function updateErrors($task) {

        try {

            if ($task) {
                $task->task_status_id = TaskStatus::STATUS_RUNNING;
                $task->save();
            }

            $count = 0;

            $activeProjects = Project::find()
                ->andWhere(['status_id' => Status::STATUS_ACTIVE])
                ->select('id')
                ->column();

            echo "ActiveProjects ".count($activeProjects).PHP_EOL;

            $activeCompetitors  = ProjectCompetitor::find()
                ->andWhere([
                    'project_id' => $activeProjects,
                    'status_id' => Status::STATUS_ACTIVE,
                ])
                ->select('competitor_id')
                ->groupBy('competitor_id')
                ->column();

//            $activeCompetitors  = Competitor::find()
//                ->andWhere([
//                    'status_id' => Status::STATUS_ACTIVE,
//                ])
//                ->select('id')
//                ->column();

            echo "activeCompetitors ".count($activeCompetitors).PHP_EOL;

            $totalCount = CompetitorItem::find()
                 ->andWhere([
                    'competitor_id'        => $activeCompetitors,
                    'status_id' => Status::STATUS_ACTIVE,
                ])
                ->count();

            echo "totalCount ".$totalCount.PHP_EOL;

            if ($task) {
                $task->total = $totalCount;
                $task->progress = $count;
                $task->save();
            }

            foreach ($activeCompetitors as $activeCompetitorId) {

                // Проекты парсинга с данным конкурентом
                $parsingProjectIds = ParsingProject::find()
                    ->andWhere([
                        'competitor_id' => $activeCompetitorId,
                        'status_id' => Status::STATUS_ACTIVE,
                    ])
                    ->select('id')
                    ->column();

                // Недавние парсинги
                $doneParsingQuery = Parsing::find()
                    ->andWhere([
                        'parsing_project_id' => $parsingProjectIds,
                    ])
                    ->andWhere([
                        '>',
                        'created_at',
                        new DateTime('-2 day')
                    ])
                    ->orderBy([
                        'created_at' => SORT_DESC
                    ]);

                // Кол-во недавних парсингов
                $parsingCount = (clone $doneParsingQuery)
                    ->count();

                $competitorItems = CompetitorItem::find()
                    ->andWhere([
                        'competitor_id' => $activeCompetitorId,
                        'status_id' => Status::STATUS_ACTIVE,
                    ])
                    ->asArray()
                    ->select(['error_last_date', 'errors_count', 'url', 'id', 'item_id'])
                    ->all();
                $ciCount = count($competitorItems);

                if (empty($parsingCount)) {
                    $count += $ciCount;
                    if ($task) {
                        $task->progress = $count;
                        $task->save();
                    }
                    echo "no recent parsing $parsingCount" . PHP_EOL;
                    continue;
                }

                echo "recent parsing count $parsingCount" . PHP_EOL;
                echo "recent parsing_project count " . count($parsingProjectIds) . PHP_EOL;
                echo "competitorItems " . count($competitorItems) . PHP_EOL;


                // Взять самый последний парсинг данного проекта
                $doneParsingId = $doneParsingQuery
                    ->select('id')
                    ->column();

                $nullify = [];
                $iterate = [];

                foreach ($competitorItems as $i => $competitorItem) {
                    $error = null;
                    foreach ($parsingProjectIds as $ip => $parsingProjectId) {
                        $error = ParsingError::find()
                            ->andWhere([
                                'parsing_project_id' => $parsingProjectId,
                                'parsing_id' => $doneParsingId,
                                'url' => $competitorItem
                            ])
                            ->select('created_at')
                            ->asArray()
                            ->scalar();
                        if ($error) break;
                    }

                    if ($error) {
                        $iterate[] = $competitorItem['id'];
                    } else {
                        if ($competitorItem['error_last_date'] !== null) {
                            $nullify[] = $competitorItem['id'];
                        }
                    }

                    if (count($nullify) > 100) {
                        CompetitorItem::updateAll([
                            'error_last_date' => null,
                            'errors_count' => 0,
                        ], [
                            'competitor_id' => $activeCompetitorId,
                            'id' => $nullify
                        ]);
                        echo 'null-100+';
                        $nullify = [];

                    }

                    if (count($iterate) > 100) {
                        CompetitorItem::updateAll([
                            'error_last_date' => new \yii\db\Expression('NOW()'),
                            'errors_count' => new \yii\db\Expression('errors_count + 1'),
                        ], [
                            'competitor_id' => $activeCompetitorId,
                            'id' => $iterate
                        ]);
                        echo 'it-100+';
                        $iterate = [];
                    }
                }

                if (count($nullify) > 0) {
                    CompetitorItem::updateAll([
                        'error_last_date' => null,
                        'errors_count' => 0,
                    ], [
                        'competitor_id' => $activeCompetitorId,
                        'id' => $nullify
                    ]);
                    echo 'null-' . count($nullify) . '+';
                }
                if (count($iterate) > 0) {
                    CompetitorItem::updateAll([
                        'error_last_date' => new \yii\db\Expression('NOW()'),
                        'errors_count' => new \yii\db\Expression('errors_count + 1'),
                    ], [
                        'competitor_id' => $activeCompetitorId,
                        'id' => $iterate
                    ]);
                    echo 'it-' . count($iterate) . '+';
                }


                $count += $ciCount;
                echo "Count $count / $totalCount " . PHP_EOL;

                if ($task) {
                    $task->progress = $count;
                    $task->save();
                }
            }


            //-
            if ($task) {
                $task->task_status_id = TaskStatus::STATUS_FINISHED;
                $task->finished_at = new DateTime();
                $task->status_id = Status::STATUS_DISABLED;
                $task->save();
            }

        } catch (\Exception $e) {
            print_r($e->getMessage());
            if ($task) {
                Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
                $task->task_status_id = TaskStatus::STATUS_QUEUED;
                $task->save();
            }
        }
    }
}