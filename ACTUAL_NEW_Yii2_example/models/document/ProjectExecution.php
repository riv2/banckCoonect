<?php
namespace app\models\document;

use app\components\DateTime;
use app\components\base\type\Document;
use app\components\base\Entity;
use app\components\exchange\Exchange;
use app\components\ValidationRules;
use app\models\enum\ErrorType;
use app\models\enum\PriceExportMode;
use app\models\enum\ProjectExecutionStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\LogKpi;
use app\models\pool\PriceRefined;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\register\Error;
use app\models\pool\LogPriceCalculation;
use app\models\pool\LogProjectExecution;
use app\models\pool\PriceCalculated;
use app\models\reference\ProjectItem;
use app\models\register\Task;
use netis\crud\db\ActiveQuery;
use yii;
use yii\caching\TagDependency;
use yii\db\Expression;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use Spatie\Async\Process;
use app\models\pool\NomenclatureDocumentItem;

/**
 * Class ProjectExecution
 * @package app\components\base\type
 *
 * Выполнение проекта
 *
 * @property string     project_id                      Проект
 * @property int        project_execution_status_id     Стадия выполнения проекта
 *
 * @property int processed_count
 * @property int exported_count
 * @property int calculated_count
 * @property int prepared_count
 *
 * @property DateTime   exported_at                     Дата экспорта
 * @property DateTime   calculated_at                   Дата расчета
 * @property DateTime   started_at                      Дата начала
 * @property DateTime   prepared_at                     Дата подготовки данных
 *
 * @property string     project_snapshot                Слепок настроек проекта JSON
 * @property array      projectSnapshot                 Слепок настроек проекта
 *
 * @property Project                            project                 Проект
 * @property ProjectExecutionStatus             projectExecutionStatus
 */

class ProjectExecution extends Document
{
    /** @var  Project кешированный проект */
    private $_project;

    // Для построения отчет Нарушения РРЦ
    public $brand_id;
    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Запуск проекта';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Запуски проекта';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['project_snapshot'], 'string'],
                [['processed_count','exported_count','calculated_count','prepared_count'], 'number']
            ],
            ValidationRules::ruleRequired('project_id','project_execution_status_id'),
            ValidationRules::ruleDateTime('exported_at','calculated_at','started_at','prepared_at'),
            ValidationRules::ruleUuid('project_id'),
            [],
            ValidationRules::ruleEnum('project_execution_status_id', ProjectExecutionStatus::className())
        );
    }

    /** @var \AMQPExchange */
    private $_rabbitExchange = null;
    /** @var \AMQPConnection*/
    private $_rabbitConnection = null;

    /**
     * @param $item
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function publishHistoryExec($item) {
        $this->connectRabbit();
        $this->_rabbitExchange->publish(Json::encode($item), getenv('RABBIT_CALC_LOG_EXEC_QUEUE'),AMQP_NOPARAM, array('delivery_mode' => AMQP_DURABLE));
        $this->_rabbitConnection->disconnect();
    }

    /**
     * @param $items
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public function publishHistory($items) {
        $this->connectRabbit();
        foreach ($items as $item) {
            $this->_rabbitExchange->publish(Json::encode($item), getenv('RABBIT_CALC_LOG_QUEUE'),AMQP_NOPARAM, array('delivery_mode' => AMQP_DURABLE));
        }
        $this->_rabbitConnection->disconnect();
    }

    /**
     * @return \AMQPExchange
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    private function connectRabbit() {
        $exchangeName = getenv('RABBIT_CALC_LOG_EXCHANGE');
        $queueName = getenv('RABBIT_CALC_LOG_QUEUE');
        $queueRoute = getenv('RABBIT_CALC_LOG_QUEUE');
        $queueName2 = getenv('RABBIT_CALC_LOG_EXEC_QUEUE');
        $queueRoute2 = getenv('RABBIT_CALC_LOG_EXEC_QUEUE');

        if (!$this->_rabbitConnection || !$this->_rabbitConnection->isConnected()) {

            $this->_rabbitConnection = new \AMQPConnection(\Yii::$app->params['amqp']);
            $this->_rabbitConnection->connect();

            $channel = new \AMQPChannel($this->_rabbitConnection);
            $this->_rabbitExchange = new \AMQPExchange($channel);
            $this->_rabbitExchange->setName($exchangeName);
            $this->_rabbitExchange->setType('topic');
            $this->_rabbitExchange->setFlags(\AMQP_DURABLE);
            $this->_rabbitExchange->declareExchange();

            $queue = new \AMQPQueue($channel);
            $queue->setName($queueName);
            $queue->setFlags(AMQP_DURABLE);
            $queue->declareQueue();
            $queueRoute = str_ireplace('_','.', $queueName);
            $queue->bind($exchangeName, $queueRoute);

            $queue2 = new \AMQPQueue($channel);
            $queue2->setName($queueName2);
            $queue2->setFlags(AMQP_DURABLE);
            $queue2->declareQueue();
            $queueRoute2 = str_ireplace('_','.', $queueName2);
            $queue2->bind($exchangeName, $queueRoute2);
        }
        return $this->_rabbitExchange;
    }

    public function __destruct()
    {
        if ($this->_rabbitConnection) {
            $this->_rabbitConnection->disconnect();
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'project_id'                    => 'Проект',
                'project_execution_status_id'   => 'Стадия',

                'project_snapshot'         => 'Слепок настроек проекта',

                'started_at'            => 'Дата начала',
                'prepared_at'           => 'Дата подготовки',
                'exported_at'           => 'Дата экспорта',
                'calculated_at'         => 'Дата расчета',

                'processed_count'       => 'Кол-во обработанных',
                'prepared_count'        => 'Кол-во подготовленных',
                'calculated_count'      => 'Кол-во расчетных цен',
                'exported_count'        => 'Кол-во выгружено',

                'project'               => 'Проект',
                'projectExecutionStatus' => 'Стадия',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations() {
        return [
            'project',
            'projectExecutionStatus',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function relations() {
        return [
            'project',
            'projectExecutionStatus',
        ];
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns() {
        return array_merge(parent::crudIndexColumns(),[
            'number',
            'project',
            'projectExecutionStatus',
            'started_at',
            'processed_count',
            'calculated_at',
            'calculated_count',
            'exported_at',
            'exported_count',
        ]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert || !$this->name) {
                $this->name = $this->project->name;
            }
            return true;
        }
        return false;
    }

    public function exportRrpViolations($forFile = true) {

        $where = ['and',
            [
                't.project_execution_id' => $this->id
            ],
            [
                't.status_id'             => Status::STATUS_ACTIVE
            ],
            [
                '<', 't.price_refined', new yii\db\Expression('"t"."price_recommended_retail" - 1')
            ],
            ['not',
                ['t.price_recommended_retail' => null]
            ],
        ];

        if ($this->brand_id) {
            $this->brand_id = explode(',', $this->brand_id);
            $where[] = ['t.brand_id' => $this->brand_id];
        }

        $competitorsData = LogPriceCalculation::find()
            ->alias('t')
            ->andWhere($where)
            ->groupBy(['competitor_id','competitor_shop_name'])
            ->select(['name' => 'competitor_shop_name', 'count' => 'count(item_id)', 'competitor_id'])
            ->asArray()
            ->all();

        $competitors = [];
        $competitorsViolations = [];
        $competitorsIds = [];
        foreach ($competitorsData as $competitor) {
            $competitorsIds[] = $competitor['competitor_id'];
            $competitors[$competitor['name']] = null;
            $competitorsViolations[$competitor['name']] = $competitor['count'];
        }
        /** @var yii\db\ActiveQuery $find */
        $find = LogPriceCalculation::find()
            ->alias('t')
            ->andWhere($where)
            ->orderBy('item_id')
            ->groupBy('item_id')
            ->select('item_id');

        $hash = md5($find->createCommand()->rawSql);

        if (Yii::$app->cache->exists("rrp_violations#".$hash.".count")) {
            $totalCount = Yii::$app->cache->get("rrp_violations#".$hash.".count");
        } else {
            $findCount  = clone $find;
            $totalCount = $findCount->count();
            Yii::$app->cache->set("rrp_violations#".$hash.".count", $totalCount, 60);
        }

//        if ($page && $page > 0 && $perPage) {
//            $find->offset((intval($page,10) - 1) * intval($perPage,10) );
//        }
//        if ($perPage) {
//            $find->limit(intval($perPage,10));
//        }

        /** @var LogPriceCalculation[] $lpc */
        $select = [
            'id' => 't.id',
            'item_id' => 't.item_id',
            'competitor_shop_name' => 't.competitor_shop_name',
            'item_name' => 't.item_name',
            'item_ym_url' => 't.item_ym_url',
            'item_brand_name' => 't.item_brand_name',
            'price_recommended_retail' => 't.price_recommended_retail',
            'price_refined' => 't.price_refined',
            'project_execution_id' => 't.project_execution_id',
            'competitor_id' => 't.competitor_id',
        ];
        $lpc = LogPriceCalculation::find()
            ->alias('t')
            ->andWhere($where)
            ->orderBy(['t.item_brand_name' => SORT_ASC]);
        if ($forFile) {
            $lpc->leftJoin([
                'ci' => CompetitorItem::find()->andWhere([
                    'status_id' => Status::STATUS_ACTIVE
                ])->andWhere([
                    'competitor_id' => $competitorsIds,
                ])->select([
                    'url',
                    'competitor_id',
                    'item_id',
                    new yii\db\Expression("'{$this->id}'::uuid  as project_execution_id")
                ])
            ] , 't.project_execution_id = ci.project_execution_id AND t.competitor_id = ci.competitor_id AND t.item_id = ci.item_id');
            $select['url'] = 'ci.url';
        }
        $lpc = $lpc
            ->asArray()
            ->select($select)->all();

        $items = [];

        foreach ($lpc as $i) {
            $iid = $i['item_id'];
            $competitorName = $i['competitor_shop_name'];
            if (!isset($items[$iid])) {
                $items[$iid] = [
                    'name'                  => $i['item_name'],
                    'ym_url'                => $i['item_ym_url'],
                    'brand'                 => $i['item_brand_name'],
                    'rrp'                   => $i['price_recommended_retail'],
                    'violations_count'      => 0,
                    'violated_competitors'  => $competitors,
                    'competitor_urls'       => array_merge($competitors,[]),
                ];
            }
            $competitorsViolations[$competitorName]++;
            $items[$iid]['violations_count']++;
            $items[$iid]['violated_competitors'][$competitorName] = $i['price_refined'];
            $items[$iid]['competitor_urls'][$competitorName] = isset($i['url'])  && $i['url'] ?$i['url'] : "/report/rrp-violation-redirect?item_id={$i['item_id']}&competitor_id={$i['competitor_id']}&lpc_id={$i['id']}&project_execution_id={$i['project_execution_id']}";
        }

        $columns = [
            [
                'label'     => 'Товар',
                'attribute' => 'name',
                'format'    => 'raw',
                'value'     => function($model) {
                    $url = $model['ym_url'];
                    if (!$url) {
                        return $model['name'];
                    }
                    return '<a href="'.$url.'" target="_blank">'.$model['name'].'</a>';
                }
            ],
            [
                'label' => 'Бренд',
                'attribute' => 'brand',
            ],
            [
                'label' => 'РРЦ',
                'format'    => 'currency',
                'attribute' => 'rrp',
                'contentOptions' => function () {
                    return ['style'=>'white-space:nowrap;'];
                }
            ],
            [
                'label' => 'Кол-во нар.',
                'attribute' => 'violations_count',
            ],
        ];

        arsort($competitorsViolations);

        foreach ($competitorsViolations as $competitorName => $violationsCount) {
            $columns[] = [
                'label'     => $competitorName,
                'attribute' => $competitorName,
                'format'    => 'raw',
                'value'     => function($item, $key, $index, $b) use($forFile) {
                    $url    = $item['competitor_urls'][is_object($b)?$b->attribute:$b['attribute']];
                    $price  = $item['violated_competitors'][is_object($b)?$b->attribute:$b['attribute']] ? number_format($item['violated_competitors'][is_object($b)?$b->attribute:$b['attribute']], 0, '.', ' ') : null;
                    if (!$url || !$price) {
                        return $price;
                    }
                    return '<a href="'.$url.'" target="_blank">'.$price.'</a>';
                },
                'contentOptions' => ['style'=>'white-space:nowrap;'],
            ];
        }

        return [
            'items'         => $items,
            'columns'       => $columns,
            'count'         => $totalCount,
            'sort'          => [
                'attributes'    => [
//                    'violations_count' => [
//                        'asc'       => ['violations_count' => SORT_ASC],
//                        'desc'      => ['violations_count' => SORT_DESC],
//                        'label'     => 'Кол-во нар.',
//                        'default'   => SORT_DESC
//                    ],
//                    'name',
//                    'brand',
                ]
            ]
        ];



    }

    /**
     * Выполнить выгрузку цен
     * @param Task $task
     * @return null;
     */
    public function taskProjectExportPrices(Task $task = null) {

        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->save();

        $this->project_execution_status_id = ProjectExecutionStatus::STATUS_EXPORTING;
        $this->save();

        //-

        $items = Item::find()
            ->alias('i')
            ->select('i.id as item_id');
        if ($this->project->price_export_mode_id == PriceExportMode::MODE_NO_RRP_ONLY) {
            $items->andWhere([
                'i.price_recommended_retail' => 0,
            ]);
        } else if ($this->project->price_export_mode_id == PriceExportMode::MODE_WITH_RRP_ONLY) {
            $items->andWhere([
                'OR',
                ['i.price_recommended_retail' => 0],
                ['pi.rrp_regulations' => true],
            ]);
        }
        if ($this->project->nomenclature_document_id) {
            $items->innerJoin([
                'pi' => NomenclatureDocumentItem::tableName()
            ], "pi.item_id = i.id AND pi.nomenclature_document_id = '".$this->project->nomenclature_document_id."'");
        } else {
            $items->innerJoin([
                'pi' => ProjectItem::tableName()
            ], "pi.item_id = i.id AND pi.project_id = '".$this->project_id."'");
        }

        $itemsCount = (clone $items);
        $task->progress = 0;
        $task->total = $itemsCount->count();
        $task->save();

        $liveDate = new DateTime(date('Y-m-d', time() + $this->project->data_life_time_span));

        try {

            $priceFormerTypeIds = $this->project->getPriceFormerTypeIds();

            if (!$priceFormerTypeIds) {
                throw new yii\base\InvalidConfigException("У проекта {$this->project} не указан ни один Тип цены Прайфсормера.");
            }

            $prices = [];
            foreach ($items->asArray()->batch(1000) as $itemsId) {
                $find = PriceCalculated::find()
                    ->alias('pc')
                    ->andWhere([
                        'project_execution_id'  => $this->id,
                        'item_id'               => ArrayHelper::getColumn($itemsId,'item_id')
                    ]);


                $pricesChunk = $find->indexBy('item_id')->select('price')->column();
                foreach ($pricesChunk as $pik => $piv) {
                    $prices[$pik] = $piv;
                }
                $count = count($prices);
                if ($count > 10000) {
                    foreach ($priceFormerTypeIds as $priceFormerTypeId) {
                        // TODO: УБРАТЬ
                        if (getenv('DISABLE_PRICE_EXPORT') === 'true') {
                            print_r($prices);
                            continue;
                        }
                        Exchange::runExport([
                            'Prices' => [
                                'prices' => $prices,
                                'live_date' => $liveDate,
                                'price_former_type_id' => $priceFormerTypeId,
                            ],
                        ]);
                    }
                    $task->progress += $count;
                    $task->save();
                    $prices = [];
                }
            }

            $count = count($prices);
            if ($count > 0) {
                foreach ($priceFormerTypeIds as $priceFormerTypeId) {
                    // TODO: УБРАТЬ
                    if (getenv('DISABLE_PRICE_EXPORT') === 'true') {
                        print_r($prices);
                        continue;
                    }
                    Exchange::runExport([
                        'Prices' => [
                            'prices' => $prices,
                            'live_date' => $liveDate,
                            'price_former_type_id' => $priceFormerTypeId,
                        ],
                    ]);
                }
                $task->progress += $count;
                $task->save();
            }

            //-
            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->save();

            $this->project_execution_status_id = ProjectExecutionStatus::STATUS_EXPORTED;
            $this->exported_count   = $task->progress;
            $this->exported_at      = $task->finished_at;
            $this->save();

            $this->project->last_export_at =  $this->exported_at;
            $this->project->last_export_count = $this->exported_count;
            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();
        } catch (\Exception $e) {
            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            $task->task_status_id   = TaskStatus::STATUS_QUEUED;
            $task->save();

            $this->project_execution_status_id  = ProjectExecutionStatus::STATUS_PAUSE_EXPORTING;
            $this->exported_count               = $task->progress;
            $this->save();

            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();
        }
    }


    /**
     * Обновить закупочные и ррц цены из прайсформера
     * @param Task $task
     * @return string
     */
    public function taskPrepareProjectExecution(Task $task = null)
    {
        $this->project_execution_status_id = ProjectExecutionStatus::STATUS_PREPARING;
        $this->save();
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_PREPARING;
        $this->started_at                   = new DateTime();
        $this->project->save();

        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->total = $this->project->getProjectItems()->count();
        $task->save();

        //-

        $projectCompetitorsIds = $this->project->projectCompetitorsIds();
        $marketplaceCompetitorsIds = $this->project->projectMarketplaceCompetitorsIds();

        $findRefinedPriceQuery = $this->project->findRefinedPriceQuery();

        $count = 0;
        try {
            ob_start();
            foreach($this->project->getProjectItems()
                        ->select('item_id')
                        ->asArray()
                        ->groupBy([
                            'item_id',
                        ])
                        ->indexBy('item_id')
                        ->batch(500) as $rows) {

                $itemIds    = array_keys($rows);

                $competitorItems = CompetitorItem::find()
                    ->andWhere([
                        'competitor_id' => $projectCompetitorsIds,
                        'item_id' => $itemIds,
                        'status_id' => Status::STATUS_ACTIVE
                    ])
                    ->groupBy([
                        'competitor_id',
                        'item_id',
                    ])
                    ->select([
                        'item_id',
                        'competitor_id',
                        new Expression("ARRAY_AGG (url) urls")
                    ])
                    ->asArray()
                    ->all();

                $relevanceDate = $this->getRelevanceDate();

                if ($competitorItems) {

                    $dateTime = new yii\db\Expression('NOW()');

                    foreach ($competitorItems as $k => $v) {
                        $competitorItems[$k]['project_id'] = $this->project_id;
                        $competitorItems[$k]['project_execution_id'] = $this->id;
                        $competitorItems[$k]['created_at'] = $dateTime;
                        $competitorItems[$k]['is_parsed'] = false;
                        $competitorItems[$k]['http404'] = false;
                        $competitorItems[$k]['out_of_stock'] = false;
                        $competitorItems[$k]['price'] = null;
                        $competitorItems[$k]['url'] = $competitorItems[$k]['urls'] ?? null;
                        unset($competitorItems[$k]['urls']);
                        $competitorItems[$k]['parsing_id'] = null;
                        $competitorItems[$k]['parsing_project_id'] = null;
                        $competitorItems[$k]['price_refined_id'] = null;
                        $competitorItems[$k]['extracted_at'] = null;

                        $findParsedItem = clone $findRefinedPriceQuery;

                        $findParsedItem
                            ->alias('t')
                            ->andWhere([
                                't.competitor_id' => $v['competitor_id'],
                                't.item_id' => $v['item_id'],
                            ])
                        ;

                        if (in_array($v['competitor_id'], $marketplaceCompetitorsIds)) {
                            $findParsedItem->orderBy([
                                't.out_of_stock' => SORT_ASC,
                                't.price' => SORT_ASC,
                                't.extracted_at' => SORT_DESC,
                            ]);
                        } else {
                            $findParsedItem->orderBy([
                                't.extracted_at' => SORT_DESC,
                            ]);
                        }

                        if ($relevanceDate) {
                            $findParsedItem->andWhere('
                                CASE WHEN c.price_lifetime > 0
                                    THEN t.extracted_at > ((NOW()) - make_interval(secs := c.price_lifetime))
                                    ELSE t.extracted_at > \'' . date('Y-m-d H:i:s', $relevanceDate) . '\'
                                END
                            ');
                        } else {
                            $findParsedItem->andWhere(['>', 't.extracted_at', new Expression('((NOW()) - make_interval(secs := c.price_lifetime))')]);
                        }

                        $parsedItem = $findParsedItem->limit(1)
                            ->asArray()
                            ->one();

                        if ($parsedItem) {
                            $competitorItems[$k]['is_parsed'] = true;
                            $competitorItems[$k]['http404'] = $parsedItem['http404'];
                            $competitorItems[$k]['out_of_stock'] = $parsedItem['out_of_stock'];
                            $competitorItems[$k]['price'] = $parsedItem['price'];
                            $competitorItems[$k]['url'] = $parsedItem['url'];
                            $competitorItems[$k]['parsing_id'] = $parsedItem['parsing_id'];
                            $competitorItems[$k]['parsing_project_id'] = $parsedItem['parsing_project_id'];
                            $competitorItems[$k]['price_refined_id'] = $parsedItem['id'];
                            $competitorItems[$k]['extracted_at'] = $parsedItem['extracted_at'];
                        }

                    }

                    LogKpi::getDb()
                        ->createCommand()
                        ->batchInsert(LogKpi::tableName(), array_keys($competitorItems[0]), $competitorItems)
                        ->execute();
                }

                $count      +=  count($itemIds);
                $task->progress = $count;
                $task->result_text = ob_get_contents();
                $task->save();
            }
            ob_end_flush();

            //-

            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->status_id        = Status::STATUS_DISABLED;
            $task->save();

            $this->project_execution_status_id = ProjectExecutionStatus::STATUS_READY_TO_CALCULATE;
            $this->prepared_count   = $task->progress;
            $this->save();

            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();
            $this->execute($task);

        } catch (\Exception $e) {
            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            $task->task_status_id   = TaskStatus::STATUS_QUEUED;
            $task->save();

            $this->project_execution_status_id = ProjectExecutionStatus::STATUS_NEW;
            $this->prepared_count   = $task->progress;
            $this->save();

            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();
        }
    }

    public function getRelevanceDate() {
        $relevanceDate = null;
        if ($this->project->price_relevance_time_span) {
            $relevanceDate = strtotime('-' . $this->project->price_relevance_time_span . ' seconds', $this->started_at->getTimestamp());
        }
        return $relevanceDate;
    }
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        LogPriceCalculation::deleteAll([
            'project_execution_id' => $this->id
        ]);
        LogProjectExecution::deleteAll([
            'project_execution_id' => $this->id
        ]);
        PriceCalculated::deleteAll([
            'project_execution_id' => $this->id
        ]);
        return parent::beforeDelete();
    }

    private $time = null;
    public function ltime($strin) {
//        if (!$this->time) {
//            $this->time = microtime(true) ;
//        }
//        $diff = round(microtime(true) - $this->time, 6);
//
//        echo sprintf("[%f] %s \n", $diff, $strin );
//
//        $this->time = microtime(true) ;
    }
    /**
     * Выполнить расчет цен проекта
     * @param Task $task
     * @return null;
     */
    public function taskProjectExecute(Task $task = null) {

        $task->task_status_id   = TaskStatus::STATUS_RUNNING;
        $task->started_at       = new DateTime();
        $task->total            = $this->project->getProjectItems()->count();
        $task->save();

        $this->project_execution_status_id  = ProjectExecutionStatus::STATUS_CALCULATING;
        $this->save();

        //-

        $count = 0;
        $errors = 0;
        $created = 0;

        TagDependency::invalidate(\Yii::$app->cache, 'calculation');
        try {
            $this->ltime('start');

//            ob_start();
            foreach ($this->project->getProjectItems()->with('item')->each(100) as $projectItem) {
                /** @var NomenclatureDocumentItem $projectItem */
                try {
                    $count++;
                    if (PriceCalculated::createCalculatePrice($this, $projectItem)) {
                        $created++;
                    }
                    echo "$count\n";
                } catch (\Exception $e) {
                    $errors++;
                    print_r($e->getMessage());
                    print_r($e->getFile());
                    print_r($e->getLine());

                    Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
                }
                if (($count) % 500 == 0) {
                    $task->progress = $count;
                    $task->errors   = $errors;
//                    $task->result_text = ob_get_contents();
                    $task->save();
                }
            }
//            ob_end_flush();
            //-

            $task->progress         = $count;
            $task->errors           = $errors;
            $task->task_status_id   = TaskStatus::STATUS_FINISHED;
            $task->finished_at      = new DateTime();
            $task->save();

            $this->project_execution_status_id          = ProjectExecutionStatus::STATUS_CALCULATED;
            $this->processed_count                      = $task->progress;
            $this->calculated_count                     = $created;
            $this->calculated_at                        = $task->finished_at;
            $this->save();

            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();

            if ($this->project->is_auto_export) {
                $this->exportPrices($task);
            }
        } catch (\Exception $e) {
            Error::logError($e, ErrorType::TYPE_TASK, Entity::Task, $task->id);
            $task->task_status_id   = TaskStatus::STATUS_FAILED;
            $task->save();
            $task->task_status_id   = TaskStatus::STATUS_QUEUED;
            $task->save();

            $this->project_execution_status_id          = ProjectExecutionStatus::STATUS_READY_TO_CALCULATE;
            $this->processed_count                      = $task->progress;
            $this->calculated_count                     = $created;
            $this->calculated_at                        = $task->finished_at;
            $this->save();

            $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
            $this->project->save();
        }
    }

    public function taskCancelProjectExportPrices(Task $task = null) {
        $this->project_execution_status_id  = ProjectExecutionStatus::STATUS_CLOSED;
        $this->save();
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
        $this->project->save();
    }

    public function taskCancelPrepareProjectExecution(Task $task = null) {
        $this->project_execution_status_id  = ProjectExecutionStatus::STATUS_CLOSED;
        $this->save();
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
        $this->project->save();
    }

    public function taskCancelProjectExecute(Task $task = null) {
        $this->project_execution_status_id  = ProjectExecutionStatus::STATUS_CLOSED;
        $this->save();
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_READY;
        $this->project->save();
    }



    public function exportPrices(Task $prevTask = null) {
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $this->project->save();

        $task2 = new Task;
        $task2->name                 = $this->name;
        $task2->requester_id         = $this->id;
        $task2->created_user_id      = ($prevTask)?$prevTask->created_user_id:null;
        $task2->updated_user_id      = ($prevTask)?$prevTask->updated_user_id:null;
        $task2->requester_entity_id  = Entity::ProjectExecution;
        $task2->task_function        = 'projectExportPrices';
        $task2->task_type_id         = TaskType::TYPE_PROJECT_EXPORT;
        $task2->enqueue();
    }

    public function execute($tryToStartNow = true, Task $prevTask = null) {
        $this->project->project_execution_status_id = ProjectExecutionStatus::STATUS_QUEUED;
        $this->project->save();

        $task2 = new Task;
        $task2->name                 = $this->name;
        $task2->requester_id         = $this->id;
        $task2->created_user_id      = ($prevTask)?$prevTask->created_user_id:null;
        $task2->updated_user_id      = ($prevTask)?$prevTask->updated_user_id:null;
        $task2->requester_entity_id  = Entity::ProjectExecution;
        $task2->task_function        = 'projectExecute';
        $task2->task_type_id         = TaskType::TYPE_PROJECT_EXECUTION;
        $task2->enqueue($tryToStartNow);
    }

    public function __toString() {
        $string = '№' . $this->number;
        if ($this->started_at && $this->started_at instanceof \DateTime) {
            $string .= ' @ '.$this->started_at->format("d.m.Y H:i:s");
        }
        return $string;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectSnapshot() {
        return Json::decode($this->project_snapshot);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function setProjectSnapshot($array) {
        $this->project_snapshot = Json::encode($array);
    }

    /**
     * @param Project|string $project
     * @throws IntegrityException
     */
    public function createProjectSnapshot($project) {
        if ($project instanceof Project) {

        } else {
            $project = Project::findOne($project);
        }
        if (!$project) {
            throw new IntegrityException("Попытка создать слепок несуществующего проекта");
        }
        $this->projectSnapshot = $project->toArray();
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (isset($changedAttributes['project_execution_status_id'])) {
            $this->project->project_execution_status_id = $this->project_execution_status_id;
            $this->project->save();
        }
    }

    /**
     * Кешированный проект
     * @param $project
     * @return Project
     */
    public function project($project = null) {
        if (!$this->_project) {
            if ($project && $project instanceof Project) {
                $this->_project = $project;
            } else {
                $this->_project = $this->project;
            }
        }
        return $this->_project;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject() {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjectExecutionStatus() {
        return $this->hasOne(ProjectExecutionStatus::className(), ['id' => 'project_execution_status_id']);
    }

    /**
     * @inheritdoc
     */
    public function processSearchToken($token, array $attributes, $tablePrefix = null)
    {
        return ['t.number' => $token];
    }
}