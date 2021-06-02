<?php
namespace app\commands;

use app\components\DataProvider;
use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\DateTime;
use app\components\exchange\Exchange;
use app\components\exchange\ProductHub;
use app\models\document\ProjectExecution;
use app\models\enum\FileFormat;
use app\models\enum\ParsingStatus;
use app\models\enum\PriceParsedStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\enum\TaskType;
use app\models\pool\LogKpi;
use app\models\pool\LogPriceCalculation;
use app\models\pool\LogProjectExecution;
use app\models\pool\ParsingBuffer;
use app\models\pool\ParsingError;
use app\models\pool\PriceRefined;
use app\models\pool\ProxyParsingProject;
use app\models\pool\Screenshot;
use app\models\pool\VpnCompetitor;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\Item;
use app\models\reference\Project;
use app\models\reference\ProjectCompetitor;
use app\models\reference\ProjectItem;
use app\models\reference\User;
use app\models\reference\Vpn;
use app\models\register\Error;
use app\models\pool\PriceParsed;
use app\models\register\FileExchange;
use app\models\register\Parsing;
use app\models\register\Task;
use app\processing\ItemProcessing;
use creocoder\flysystem\FtpFilesystem;
use GuzzleHttp\Client;
use yii;
use yii\console\Controller;
use yii\helpers\Json;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Settings;

class UtilController extends Controller
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


    public function actionRelaunchErrors() {

        if ($this->processIsRun('util/relaunch-errors') > 2) {
            return;
        }
        /** @var Parsing[] $parsings */
        $parsings = Parsing::find()
            ->andWhere([
                'parsing_status_id' => ParsingStatus::STATUS_DONE,
                'status_id'         => Status::STATUS_ACTIVE,
                'is_test'           => false,
                'next_attempt_id'   => null,
                'parallel_is_main'  => true,
            ])
            ->andWhere([
                '>', 'errors_count', 0
            ])
            ->andWhere([
                '<', 'attempt',     2
            ])
            ->all();


        foreach ($parsings as $parsing) {
            $childParsings = Parsing::find()
                ->andWhere([
                    'parallel_main_id' => $parsing->id
                ])
                ->all();

            $allDone = true;

            foreach ($childParsings as $childParsing) {
                $allDone = $allDone && ($childParsing->parsing_status_id === ParsingStatus::STATUS_DONE);
            }

            if (!$allDone) {
                continue;
            }

            $ids = yii\helpers\ArrayHelper::getColumn($childParsings,'id');

            if (ParsingError::find()
                ->andWhere([
                    'parsing_id' => $ids
                ])
                ->andWhere(['is not', 'url', null])
                ->count() > 0) {

                $scope = Json::decode($parsing->scope_info);
                $scope['from_errors'] = $ids;
                $scope['attempt'] = $parsing->attempt + 1;
                $scope['name'] = 'Перепарсинг '.$parsing->name;
                $scope['regions'] = $parsing->region_id;

                $id = $parsing->parsingProject->execute($scope);

                if ($id) {
                    $parsing->next_attempt_id = $id;
                }
                $parsing->save();
            }
        }
    }

    /**
     * Дедубликация товаров из PDM
     */
    public function actionItemDeduplicate() {

        if ($this->processIsRun('util/item-deduplicate') > 2) {
            return;
        }

        $phub = new ProductHub;

        foreach (Item::find()
                     ->andWhere([
                         'is_duplicate' => true
                     ])
                     ->batch(5000) as $items) {

            /** @var Item[] $items */
            foreach ($items as $item) {
                echo ".";
                if (!$item->main_id) {
                    $mainId = $phub->deduplicate($item->id);
                    $item->main_id = $mainId;
                    $item->save();
                    echo "[Phub request]";
                }

                if ($item->main_id) {
                    /** @var string[][] $competitorItems */
                    $competitorItems = CompetitorItem::find()
                        ->andWhere([
                            'item_id' => $item->id,
                            'status_id' => Status::STATUS_ACTIVE,
                        ])
                        ->asArray()
                        ->all();

                    if ($competitorItems && count($competitorItems) > 0) {
                        foreach ($competitorItems as $i => $competitorItem) {
                            unset($competitorItem['id']);
                            unset($competitorItem['index']);

                            $competitorItem['item_id'] = $item->main_id;
                            if (! CompetitorItem::find()
                                ->andWhere([
                                    'competitor_id' => $competitorItem['competitor_id'],
                                    'item_id'       => $competitorItem['item_id'],
                                    'status_id' => Status::STATUS_ACTIVE,
                                ])
                                ->exists()) {

                                try {
                                    CompetitorItem::getDb()
                                        ->createCommand()
                                        ->insert(CompetitorItem::tableName(), $competitorItem)
                                        ->execute();
                                    echo "[Urls clone ". count($competitorItems)."]";
                                } catch (yii\db\Exception $e) {
                                    echo "[Error insert]";
                                }

                            }
                        }
                    }
                }

            }
        }

    }

    /**
     * Построение логов расчетов цен
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     * @throws yii\db\Exception
     */
    public function actionCalcLog() {

        if ($this->processIsRun('util/calc-log') > 5) {
            return;
        }
        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();

        $channel = new \AMQPChannel($connection);

        $queue = new \AMQPQueue($channel);
        $queue->setName(getenv('RABBIT_CALC_LOG_QUEUE'));
        $queue->setFlags(AMQP_DURABLE);

        for ($i=0 ; $i<20000; $i++) {
            $item  = 1;
            $items = [];
            while (count($items) < 1000 && $item) {
                $item = $queue->get(AMQP_AUTOACK);
                if ($item) {
                    $itemJson = Json::decode($item->getBody());
                    $itemJson['regions'] = '';
                    unset($itemJson[0],$itemJson['0']);
                    $items[] = $itemJson;

                    LogKpi::updateAll([
                        'is_used_in_calc' => true,
                        'url' => $itemJson['url'],
                        'price' => $itemJson['price_refined'],
                        'extracted_at' => $itemJson['extracted_at'],
                        'price_refined_id' => $itemJson['price_refined_id'],
                        'calculated_at' => $itemJson['created_at'],
                        'out_of_stock' => $itemJson['out_of_stock'],
                    ], [
                        'project_execution_id' => $itemJson['project_execution_id'],
                        'competitor_id' => $itemJson['competitor_id'],
                        'item_id' => $itemJson['item_id'],
                    ]);
                }
            }
            if (count($items) > 0) {
                LogPriceCalculation::getDb()
                    ->createCommand()
                    ->batchInsert(LogPriceCalculation::tableName(), array_keys($items[0]), $items)
                    ->execute();
            }
        }

        $connection->disconnect();
    }

    /**
     * Построение логов расчетов цен
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     * @throws yii\db\Exception
     */
    public function actionCalcLogExec() {

        if ($this->processIsRun('util/calc-log-exec') > 2) {
            return;
        }
        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();

        $channel = new \AMQPChannel($connection);

        $queue = new \AMQPQueue($channel);
        $queue->setName(getenv('RABBIT_CALC_LOG_EXEC_QUEUE'));
        $queue->setFlags(AMQP_DURABLE);

        for ($i=0 ; $i<2000; $i++) {
            $item  = 1;
            $items = [];
            while (count($items) < 1000 && $item) {
                $item = $queue->get(AMQP_AUTOACK);
                if ($item) {
                    $itemJson = Json::decode($item->getBody());
                    if ($itemJson) {
                        unset($itemJson[0],$itemJson['0']);
                        if (isset($itemJson['created_at']) && is_array($itemJson['created_at'])) {
                            $itemJson['created_at'] = date('Y-m-d H:i:s');
                        }
                        $items[] = $itemJson;
                    }
                }
            }
            if (count($items) > 0) {
                LogProjectExecution::getDb()->createCommand()->batchInsert(LogProjectExecution::tableName(), array_keys($items[0]), $items)->execute();
            }
        }

        $connection->disconnect();
    }

    /**
     * Вычистить из ребита висящие очереди
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function actionClearRabbit() {
        $client = new Client;

        $amqp = Yii::$app->params['amqp'];

        $response = $client->get("http://{$amqp['host']}:15672/api/queues?columns=name,consumers,messages,idle_since", [
            'auth' => [$amqp['login'],$amqp['password']]
        ]);

        $body = $response->getBody();
        $queues = Json::decode($body, true);

        if (isset($queues['error'])) {
            throw new \Exception($body);
        }

        $cancelledParsings = Parsing::find()
            ->select('id')
            ->andWhere(['parsing_status_id' => ParsingStatus::STATUS_CANCELED])
            ->column();
        $queuesToRemove = array_map(function($val) { return 'p_' . $val; }, $cancelledParsings);

        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();
        $channel = new \AMQPChannel($connection);
        $sutki = 3600 * 24;
        $now = strtotime("now");
        foreach ($queues as $queueInfo) {
            $queueWasRemoved = false;
            if (isset($queueInfo['idle_since'])) {
                if (intval($queueInfo['consumers']) === 0 && $now - strtotime($queueInfo['idle_since']) > $sutki) {
                    $queue = new \AMQPQueue($channel);
                    $queue->setName($queueInfo['name']);
                    $queue->delete();
                    $queueWasRemoved = true;
                    echo 'Queue "' . $queueInfo['name'] . '" was removed by "idle_since"' . PHP_EOL;
                }
            }
            if (!$queueWasRemoved && $queueInfo['consumers'] === 0 && $queueInfo['messages'] === 0) {
                $queue = new \AMQPQueue($channel);
                $queue->setName($queueInfo['name']);
                $queue->delete();
                $queueWasRemoved = true;
                echo 'Queue "' . $queueInfo['name'] . '" was removed by no messages' . PHP_EOL;
            }
            if (!$queueWasRemoved && in_array($queueInfo['name'], $queuesToRemove)) {
                $queue = new \AMQPQueue($channel);
                $queue->setName($queueInfo['name']);
                $queue->delete();
                echo 'Queue "' . $queueInfo['name'] . '" was removed by cancelled parsing' . PHP_EOL;
            }
        }
        $connection->disconnect();
    }


    /**
     * Перевод цен из буффера в спарсенные (для Content Downloader)
     */
    public function actionBufferPrices() {
        if ($this->processIsRun('util/buffer-prices') > 2) {
            return;
        }
        $i = 0;
        foreach (ParsingBuffer::find()
                     ->andWhere([
                         'is_error' => false
                     ])
                     ->batch(5000) as $buffers) {
            foreach ($buffers as $buffer) {
                try {
                    /** @var ParsingBuffer $buffer */
                    $parsedPrice = new PriceParsed;
                    if ($i >= 10) {
                        $i = 0;
                    }
                    $data = $buffer->data;
                    $data['thread'] = $i;
                    $data['parsing_id'] = 'aaa00002-36e5-4b35-bd74-cb971a8d9335';
                    $data['parsing_project_id'] = 'aaa00001-36e5-4b35-bd74-cb971a8d9335';
                    $parsedPrice->importOneFromFile($data);
                    $i++;
                    $buffer->delete();
                } catch (\Exception $e) {
                    $buffer->is_error = true;
                    $buffer->error_message = Error::extractMessage($e->getMessage());
                    $buffer->save(false);
                }
            }
        }
    }

    public function actionResetCache() {
        Yii::$app->db->schema->refresh();
        TaskType::resetCache();
        Entity::resetCache();
        ParsingStatus::resetCache();
    }

    public function actionCreateAdmin($username = 'pricing-admin', $reset = '', $email = 'thejet@yandex.ru') {
        $security = Yii::$app->security;
        $password = "111111";
        $password = $security->generatePasswordHash($password);

        $user = User::findOne([ 'username' => $username]);
        if (!$user) {
            //echo $username.PHP_EOL;
            $user = new User();
            $user->email    = $email;
            $user->username     = $username;
            $user->password = $password;
            $user->is_active = true;
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $user->email_verified = true;
            }
            $user->created_at = gmdate('Y-m-d H:i:s');
            $user->access_token = $security->generateRandomString();
            $user->auth_key     = $security->generateRandomString();
            $user->firstname    = 'Админыч';
            $user->lastname     = 'Админ';

            if ($user->validate()) {
                $user->save();
            } else {
                throw new \Exception(print_r($user->getErrors(),true));
            }
        }

        if ($reset !== '') {
            Entity::resetCache();

            $entities = Entity::find()->select(['class_name'])->column();
            $entities[] = 'usr';
            $entities[] = 'app\components\base\Entity';
            $entities[] = 'app\models\SqlogError';

            $auth = Yii::$app->authManager;
            $adminRole = $auth->getRole('admin');
            if (!$adminRole) {
                $adminRole = $auth->createRole('admin');
                $auth->add($adminRole);
                //$auth->remove($adminRole);
            }
            foreach ($entities as $model) {
                foreach (['create', 'read', 'update', 'delete'] as $opName) {
                    $item = $auth->getPermission($model . '.' . $opName);
                    if ($item !== null) {
                        $auth->remove($item);
                    }
                }
            }
           // $role = $auth->createRole('admin');
           // $auth->add($role);
            foreach ($entities as $model) {
                foreach (['create', 'read', 'update', 'delete'] as $opName) {
                    $authItem = $auth->createPermission($model . '.' . $opName);
                    $auth->add($authItem);
                    $auth->addChild($adminRole, $authItem);
                }
            }
        }
        if (!Yii::$app->authManager->getAssignment('admin', $user->id)) {
            Yii::$app->authManager->assign(Yii::$app->authManager->getRole('admin'), $user->id);
        }

    }

    /**
     * Кешировать кол-ва сущностей в справочниках в которых это необходимо
     * @throws yii\db\IntegrityException
     */
    public function actionCacheCounts() {
        $entities = Entity::getEnumArray();
        foreach ($entities as $entity) {
            $class = $entity['class_name'];
            if (method_exists($class, 'dataProviderCountNeedCaching')) {
                $needCountCaching = forward_static_call([$entity['class_name'], 'dataProviderCountNeedCaching']);
                if ($needCountCaching) {
                    /** @var BaseModel $object */
                    /** @var DataProvider $dataProvider */
                    $object = new $class();

                    $dataProvider = new DataProvider([
                        'query'         => $object->crudSearch(),
                        'sort'          => $object->getSort(),
                        'pagination'    => [
                            'pageSizeLimit'     => [-1, 0x7FFFFFFF],
                            'defaultPageSize'   => 50,
                        ],
                    ]);

                    echo $dataProvider->resetTotalCountCache().PHP_EOL;
                }
            }
        }
    }


    public function actionClearOldData($days = 3) {


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

        Parsing::deleteAll([
            'and', ['<', 'created_at', $date]
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

    }

    public function actionItemUpdatePrices() {
        Item::updateAllPrices(['tryToStart' => true]);
    }

    public function actionItemUpdateUrls() {
        Item::updateAllUrls(['tryToStart' => true]);
    }

    /**
     * Очистка связок впн-парсинг у завершенных парсингов
     */
    public function actionFreeVpns()
    {
        if ($this->processIsRun('util/free-vpns') > 2) {
            return;
        }
        $idsToDelete = VpnCompetitor::find()
            ->alias('vc')
            ->select('vc.id')
            ->innerJoin(
                ['p' => Parsing::tableName()],
                'p.id = vc.parsing_id'
            )
            ->andWhere(['NOT IN', 'p.parsing_status_id', [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED]])
            ->orWhere(['!=', 'p.status_id', Status::STATUS_ACTIVE])
            ->column();
        VpnCompetitor::deleteAll(['id' => $idsToDelete]);
    }

    /**
     * Обработка регионов
     */
    public function actionFreeRegions()
    {
        if ($this->processIsRun('util/free-regions') > 2) {
            return;
        }
        $parsingsQuery = Parsing::find()
            ->select([
                'ids' => 'json_agg(json_build_object(\'robot_id\', robot_id, \'id\', id))',
                'parsing_project_id'
            ])
            ->andWhere('region_id IS NOT NULL')
            ->andWhere([
                'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                'status_id' => Status::STATUS_ACTIVE,
                'priority' => 0,
            ])
            ->groupBy(['region_id', 'parsing_project_id'])
            ->asArray();
        $projectsInProcess = [];
        $client = new Client([
            'timeout'   => 60.0,
            'headers' => [
                'Accept'     => 'application/json',
            ]
        ]);
        foreach ($parsingsQuery->each() as $parsingsData) {
            if (!in_array($parsingsData['parsing_project_id'], $projectsInProcess)) {
                $projectsInProcess[] = $parsingsData['parsing_project_id'];
                continue;
            }
            $dataArr = json_decode($parsingsData['ids'], true);
            Parsing::updateAll([
                'parsing_status_id' => ParsingStatus::STATUS_QUEUED
            ], [
                'id' => yii\helpers\ArrayHelper::getColumn($dataArr, 'id')
            ]);
            foreach ($dataArr as $data) {
                $response = $client->request('GET','http://' . $data['robot_id'] . ':4000/kill-parsing/' . $data['id']);

                print_r($response->getBody()->getContents() . "\n");
            }
        }
    }

    /**
     * обновление данных во вкладке "Парсинги" на главной странице
     */
    public function actionUpdateActiveParsingsData()
    {
        $activeParsingsIds = Parsing::find()
            ->select('id')
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING],
            ])
            ->column();

        $activeParsingsQuery = (new yii\db\Query)
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
                        'COUNT(CASE WHEN item_id IS NOT NULL AND out_of_stock = false then 1 ELSE NULL END) in_stock_count',
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

        Yii::$app->cache->set('active_parsings_data', $activeParsingsQuery->all());

        $query = Parsing::find()
            ->alias('p')
            ->select([
                '*',
                'success' => '(CASE WHEN ppa.items_count IS NOT NULL AND ppa.items_count > 0 THEN ppa.items_count ELSE ppa.items_count_by_time END)',
                'errors' => 'pe.errors_count',
                'all_parsed' => 'ppac.items_count',
                'all_errors' => 'pec.errors_count',
                'last_count' => '(p.global_count - (CASE WHEN ppac.items_count IS NOT NULL AND ppac.items_count > 0 THEN ppac.items_count ELSE ppac.items_count_by_time END) - (CASE WHEN pec.errors_count IS NOT NULL THEN pec.errors_count ELSE 0 END))'            ])
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

        Yii::$app->cache->set('active_parsings_data_times', $timeData);
    }

    public function actionSmsNotification()
    {
        $endTimes = Yii::$app->cache->get('active_parsings_data_times');
        $lateTime = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d 22:00:00'))->getTimestamp();

        foreach ($endTimes as $parsingId => $endTime) {
            if (!$endTime || $endTime > $lateTime) {
                $parsing = Parsing::findOne($parsingId);
                if ($parsing->parsingProject->signals_enabled) {
                    $client = new Client;
                    $text = '[Pricing]: Присутствуют проблемы со сбором - ' . $parsing->parsingProject->name;

                    $response = $client->post('http://sms.vseinstrumenti.ru/api/v1/sms/send-to', [
                        'auth' => [Yii::$app->params['sms']['login'], Yii::$app->params['sms']['password']],
                        'json' => [
                            [
                                'to' => '9087512048',
                                'text' => $text,
                            ],
                            [
                                'to' => '9307470408',
                                'text' => $text,
                            ]
                        ],
                    ]);

                    $body = $response->getBody()->getContents();
                    print_r($body);
                    return;
                }
            }
        }
    }

    public function actionFreeProxies()
    {
        if ($this->processIsRun('util/free-vpns') > 2) {
            return;
        }
        $idsToDelete = ProxyParsingProject::find()
            ->alias('ppp')
            ->select('ppp.id')
            ->innerJoin(
                ['p' => Parsing::tableName()],
                'p.id = ppp.parsing_id'
            )
            ->andWhere(['NOT IN', 'p.parsing_status_id', [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED]])
            ->orWhere(['!=', 'p.status_id', Status::STATUS_ACTIVE])
            ->column();
        ProxyParsingProject::deleteAll(['id' => $idsToDelete]);
    }
}