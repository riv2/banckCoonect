<?php

namespace app\models\register;

use AMQPChannel;
use AMQPChannelException;
use AMQPConnection;
use AMQPConnectionException;
use AMQPQueue;
use AMQPQueueException;
use app\components\base\type\Register;
use app\components\DateTime;
use app\components\ValidationRules;
use app\models\enum\ParsingStatus;
use app\models\enum\Region;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\enum\TaskStatus;
use app\models\pool\ParsingProjectProxy;
use app\models\pool\ParsingProjectVpnBan;
use app\models\pool\ProxyParsing;
use app\models\pool\ProxyParsingProject;
use app\models\pool\ReportMatching;
use app\models\pool\VpnCompetitor;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use app\models\reference\Setting;
use app\models\reference\Vpn;
use yii;
use yii\base\InvalidParamException;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * Class Parsing
 * @package app\models\register
 *
 * Парсинг
 *
 * @property int region_id
 * @property int source_id
 * @property int global_count
 * @property int parsed_count
 * @property int total_count
 * @property int errors_count
 * @property int unreached_count
 * @property int parsing_status_id
 * @property int success_count
 * @property int requests_count
 * @property int connected_count
 * @property int in_stock_count
 * @property int passed_filter_count
 * @property string name
 * @property string parsing_project_id
 * @property DateTime started_at
 * @property DateTime finished_at
 * @property bool parsing_type
 * @property bool prepare_pages
 * @property bool parallel_is_main
 * @property string parallel_main_id
 * @property string proxies
 *
 * @property string scope_info
 * @property array scope
 * @property string regions
 * @property string browser
 * @property bool is_phantom
 * @property bool is_test
 * @property int parallel_droids
 * @property string robot_id
 * @property string hash
 * @property string droid_type
 * @property int attempt
 * @property string next_attempt_id
 * @property int priority
 *
 * @property ParsingProject parsingProject
 * @property ParsingStatus parsingStatus
 * @property Region region
 * @property Source source
 * @property int totalErrors
 * @property float errorsPercent
 * @property float successPercent
 */
class Parsing extends Register
{

    /**
     * Это свойство используется для построения хеша hash на основе parsing_project_id, parallelIndex и robot_id
     * для группировки по хешу при использовании parallel_droids
     * @var int
     */
    public $parallelIndex = 1;

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Парсинг';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Парсинг';
    }

    /**
     * @inheritdoc
     */
    public static function relations()
    {
        return array_merge(
            parent::relations(),
            [
                'parsingProject',
                'parsingStatus',
                'region',
                'source',
            ]
        );
    }

    /**
     * @return ActiveQuery
     */
    public static function getActiveParsings()
    {
        return Parsing::find()
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED]
            ]);
    }

    /**
     * Создать на основании Проекта парсинга
     * @param ParsingProject $parsingProject - экземпляр проекта парсинга
     * @return Parsing
     */
    public static function createFromProject(ParsingProject $parsingProject)
    {
        $parsing = new Parsing();
        $parsing->loadDefaultValues();
        $parsing->parsing_project_id = $parsingProject->id;
        $parsing->source_id = $parsingProject->source_id;
        $parsing->parsing_type = $parsingProject->parsing_type;
        $parsing->is_phantom = $parsingProject->is_phantom;
        $parsing->browser = $parsingProject->browser;
        $parsing->prepare_pages = $parsingProject->prepare_pages;
        $parsing->droid_type = $parsingProject->droid_type;
        return $parsing;
    }

    /**
     * @param $parsingId
     * @throws AMQPChannelException
     * @throws AMQPConnectionException
     * @throws AMQPQueueException
     */
    public static function cancel($parsingId)
    {
        /** @var Parsing $parsing */
        $parsing = Parsing::findOne($parsingId);
        if ($parsing) {
            $connection = new AMQPConnection(Yii::$app->params['amqp']);
            $connection->connect();
            $channel = new AMQPChannel($connection);
            $queue = new AMQPQueue($channel);
            $queue->setName($parsing->getQueueName());
            $queue->delete();
            $connection->disconnect();

            Yii::$app->redis->executeCommand('PUBLISH', ['parsing-control', Json::encode(['id' => $parsingId, 'parsing_status_id' => ParsingStatus::STATUS_CANCELED, 'finished_at' => date('Y-m-d H:i:s')])]);

            Parsing::updateAll([
                'parsing_status_id' => ParsingStatus::STATUS_CANCELED
            ], [
                'parallel_main_id' => $parsing->parallel_main_id,
                'parsing_status_id' => [
                    ParsingStatus::STATUS_QUEUED,
                    ParsingStatus::STATUS_PROCESSING,
                    ParsingStatus::STATUS_NEW,
                    ParsingStatus::STATUS_PAUSED,
                    ParsingStatus::STATUS_HANGED,
                ]
            ]);
        }
    }

    /**
     * Получить имя очереди RabbitMQ
     * @return string
     */
    public function getQueueName()
    {
        $prefix = getenv('RABBIT_PARSING_QUEUE_PREFIX') ?: 'p_';
        return $prefix . $this->id;
    }

    /**
     * @param null $robotId
     * @return array
     */
    public function getSettings($robotId = null)
    {

        $parsingSettings = $this->parsingProject->getSettings(true, $robotId);


        $settings = array_merge($parsingSettings,
            [
                'id' => $this->id,
                'parsing_project_id' => $this->parsing_project_id,
                'name' => $this->name,
                'domains' => $this->parsingProject->competitor ? $this->parsingProject->competitor->getCompetitorShopDomains()->select('name')->column() : [],
                'region_id' => $this->region_id,
                'regions' => $this->regions,
                'source_id' => $this->source_id,
                'competitor_id' => $this->parsingProject->competitor_id,
                'cookies' => $this->parsingProject->getRegionsCookies($this->region_id),
                'skip' => null
            ]);

        if ($this->parsingProject->vpn_type) {
            $settings['vpn'] = $this->getVpnSettings();
        }

        // Вырехать нужную часть прокси из настроек проекта на лету
//        if ($this->parsingProject->parallel_droids > 1) {
//            $proxies = $parsingSettings['proxies'];
//            $parallelTotal = $this->parsingProject->parallel_droids;
//            if (count($proxies) < $parallelTotal) {
//                $settings['proxies'] = $proxies;
//            } else {
//                $chunkSize = floor(count($proxies) / $parallelTotal);
//                ProxyParsingProject::deleteAll(['parsing_id' => $this->id]);
//                $excludedProxy = ProxyParsingProject::find()
//                    ->select('proxy_id')
//                    ->andWhere([
//                        'parsing_project_id' => $this->parsing_project_id,
//                    ])
//                    ->column();
//                $proxies = array_slice(array_filter($proxies, function ($proxy) use ($excludedProxy) {
//                    return !in_array($proxy, $excludedProxy);
//                }), 0, $chunkSize);
//                if (!count($proxies)) {
//                    throw new yii\base\UserException('Не хватило прокси для парсинга ' . $this->id);
//                }
//                ProxyParsingProject::getDb()
//                    ->createCommand()
//                    ->batchInsert(
//                        ProxyParsingProject::tableName(),
//                        ['proxy_id', 'parsing_project_id', 'parsing_id'],
//                        array_map(function ($proxy) {
//                            return [
//                                'proxy_id' => $proxy,
//                                'parsing_project_id' => $this->parsing_project_id,
//                                'parsing_id' => $this->id,
//                            ];
//                        }, $proxies)
//                    )
//                    ->execute();
//                $settings['proxies'] = $proxies;
//            }
//        }

        if ($this->parsing_type === 'collecting') {
            $competitorId = $this->parsingProject->competitor_id;

            $settings['skip'] = HoradricCube::find()
                ->andWhere([
                    'competitor_id' => $competitorId
                ])
                ->select(['competitor_id', 'competitor_item_name'])
                ->asArray()
                ->all();
        }

        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleUuid(['parsing_project_id', 'next_attempt_id', 'parallel_main_id']),
            parent::rules(),
            [
                [['scope_info', 'name', 'robot_id', 'hash', 'droid_type', 'browser'], 'string'],
                [['regions', 'proxies'], 'safe'],
                [['parsing_type'], 'in', 'range' => ['normal', 'collecting', 'matching']],
                [['is_phantom', 'prepare_pages', 'is_test', 'parallel_is_main'], 'boolean'],
                [['attempt', 'parallel_droids', 'parallelIndex', 'parsed_count', 'total_count', 'errors_count', 'unreached_count', 'requests_count', 'connected_count', 'with_retries_count', 'success_count', 'passed_filter_count', 'in_stock_count', 'global_count'], 'number'],
            ],
            ValidationRules::ruleDateTime('started_at', 'finished_at'),
            ValidationRules::ruleDefault('parallel_droids', 1),
            ValidationRules::ruleDefault('parsing_status_id', ParsingStatus::STATUS_NEW),
            ValidationRules::ruleDefault(['parsed_count', 'total_count', 'errors_count'], 0)
        );
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'parsing_project_id' => 'Проект парсинга',
                'parsingProject' => 'Проект',
                'parsing_status_id' => 'Статус',
                'parsingStatus' => 'Статус',
                'started_at' => 'Начато',
                'finished_at' => 'Завершено',
                'region_id' => 'Регион',
                'region' => 'Регион',
                'regions' => 'Регионы',
                'source_id' => 'Источник',
                'source' => 'Источник',
                'total_count' => 'Урлов',
                'global_count' => 'Всего урлов',
                'requests_count' => 'Обработано',
                'unreached_count' => 'Не соед.',
                'connected_count' => 'Соед.',
                'passed_filter_count' => 'Прошло фильтр',
                'in_stock_count' => 'В наличии',
                'parsed_count' => 'Спарсено',
                'errors_count' => 'Ошибок',
                'with_retries_count' => 'Попыток',
                'parsing_type' => 'Тип проекта',
                'prepare_pages' => 'Готовить страницы',
                'robot_id' => 'Робот',
                'parallel_droids' => 'Параллельно дроидов',
                'droid_type' => 'Тип дроида'
            ]
        );
    }

    /**
     * Получить имя роута RabbitMQ
     * @return string
     */
    public function getQueueRoute()
    {
        return 'p.' . $this->id;
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations()
    {
        return array_merge(
            parent::crudIndexSearchRelations(),
            [
                'parsingProject',
                'parsingStatus',
                'region',
                'source',
            ]
        );
    }

    public function getErrorsPercent()
    {
        return floor($this->getTotalErrors() * 100 / $this->total_count);
    }

    public function getTotalErrors()
    {
        return $this->errors_count + $this->unreached_count;
    }

    public function getSuccessPercent()
    {
        return ceil($this->success_count * 100 / $this->total_count);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return array_merge(
            parent::crudIndexColumns(),
            [
                'created_at',
                'started_at',
                'updated_at',
                'finished_at',
                'name',
                'parsingProject',
                'parsingStatus',
                'total_count',
                'parsed_count',
                'errors_count',
                'in_stock_count',
                'passed_filter_count',
                'region',
                'robot_id',
                'droid_type',
            ]
        );
    }

    public function setScope($scopeInfo)
    {
        try {
            $this->scope_info = Json::encode($scopeInfo);
        } catch (InvalidParamException $e) {
            $this->scope_info = null;
        }
    }

    public function getScope()
    {
        try {
            return Json::decode($this->scope_info, true);
        } catch (InvalidParamException $e) {
            return [];
        }
    }

    public function beforeSave($insert)
    {
        if (!$this->region_id) {
            $this->region_id = null;
        }
        if (!$this->source_id) {
            $this->source_id = null;
        }
        if ($insert) {
            if (!$this->id) {
                $uuid = static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar();
                $this->id = $uuid;
            }
            $this->hash = $this->parsing_project_id . '-' . $this->robot_id . '-' . $this->parallelIndex;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (isset($changedAttributes['parsing_status_id']) && $this->parsing_status_id == ParsingStatus::STATUS_DONE) {
            $this->parsingProject->last_parsing_id = $this->id;
            $this->parsingProject->save();
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function assignRobot()
    {
        $robotId = null;
        $activeRobots = Robot::find()
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->select('id')
            ->column();

        $activeRobots = $activeRobots ?: [];

        $statRobots = Parsing::find()
            ->andWhere([
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING, ParsingStatus::STATUS_QUEUED],
                'status_id' => Status::STATUS_ACTIVE,
                'is_test' => false,
                'robot_id' => $activeRobots
            ])
            ->orderBy(['c' => SORT_DESC])
            ->groupBy('robot_id')
            ->indexBy('robot_id')
            ->select(['robot_id', 'count(id) c'])
            ->asArray()
            ->column();

        $active = array_combine($activeRobots, $activeRobots);
        foreach ($statRobots as $statRobotId) {
            if (isset($active[$statRobotId])) {
                unset($active[$statRobotId]);
            }
            $robotId = $statRobotId;
        }
        $active = array_keys($active);
        if (count($active) > 0) {
            $robotId = $active[0];
        }
        $this->robot_id = $robotId;
    }

    /**
     * @return ActiveQuery
     */
    public function getParsingProject()
    {
        return $this->hasOne(ParsingProject::className(), ['id' => 'parsing_project_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getParsingStatus()
    {
        return $this->hasOne(ParsingStatus::className(), ['id' => 'parsing_status_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSource()
    {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_id']);
    }


    public function taskReportMatching(Task $task = null)
    {
        $task->task_status_id = TaskStatus::STATUS_RUNNING;
        $task->started_at = new DateTime();
        $task->save();


        ReportMatching::create($this);

        $task->progress++;
        $task->save();


        $task->task_status_id = TaskStatus::STATUS_FINISHED;
        $task->finished_at = new DateTime();
        $task->had_errors = 0;
        $task->save();

    }

    public function getVpnSettings()
    {
        $vpn = null;
        if (!$this->parsingProject->vpn_type) {
            return null;
        }
        $vpnQuery = Vpn::find()
            ->alias('v')
            ->innerJoin(
                ['vp' => VpnCompetitor::tableName()],
                'vp.vpn_id = v.id AND vp.parsing_id = \'' . $this->id . '\''
            )
            ->asArray();
        if (!$vpnQuery->exists()) {
            $this->assignNewVpn();
        }
        $vpn = $vpnQuery->one();
        $vpn['errors_limit'] = Setting::getValue('vpn_error_limit', 5);
        return $vpn;
    }

    /**
     * Привязать свободный VPN-сервер
     */
    public function assignNewVpn()
    {
        if (!$this->parsingProject->vpn_type || !$this->id) {
            return;
        }
        $existsVpnQuery = VpnCompetitor::find()->select('vpn_id')->andWhere(['parsing_id' => $this->id]);
        if ($existsVpnQuery->exists()) {
            $existsVpnId = $existsVpnQuery->scalar();
            if (ParsingProjectVpnBan::updateAll([
                    'banned_at' => date('Y-m-d H:i:s'),
                ], [
                    'parsing_project_id' => $this->parsingProject->id,
                    'vpn_id' => $existsVpnId,
                ]) === 0) {
                ParsingProjectVpnBan::getDb()
                    ->createCommand()
                    ->insert(ParsingProjectVpnBan::tableName(), [
                        'id' => static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                        'parsing_project_id' => $this->parsingProject->id,
                        'vpn_id' => $existsVpnId,
                        'banned_at' => date('Y-m-d H:i:s'),
                    ])
                    ->execute();
            }
        }
        $query = Vpn::find()
            ->alias('v')
            ->select('v.id, ppvb.banned_at')
            ->andWhere([
                '(' . VpnCompetitor::find()
                    ->alias('vp')
                    ->select('count(*)')
                    ->andWhere('vp.vpn_id = v.id')
                    ->andWhere(['vp.competitor_id' => $this->parsingProject->competitor_id])
                    ->createCommand()
                    ->getRawSql()
                . ')' => 0,
                'status_id' => Status::STATUS_ACTIVE,
            ])
            //->andWhere(['<=', 'until', date('Y-m-d')])
            ->leftJoin(
                ['ppvb' => ParsingProjectVpnBan::tableName()],
                'vpn_id = v.id AND ppvb.parsing_project_id = \'' . $this->parsingProject->id . '\''
            )
            ->orderBy('(CASE WHEN banned_at IS NULL THEN 0 ELSE 1 END), banned_at ASC');
        if ($this->parsingProject->vpn_type === 'use') {
            $query->andWhere(['v.id' => $this->parsingProject->vpns]);
        }
        //$query->offset('floor(random()*(' . $query->count() . '))');
        $vpnId = $query->scalar();
        if ($vpnId) {
            VpnCompetitor::deleteAll([
                'parsing_id' => $this->id,
            ]);
            VpnCompetitor::getDb()
                ->createCommand()
                ->insert(VpnCompetitor::tableName(), [
                    'id' => static::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                    'vpn_id' => $vpnId,
                    'competitor_id' => $this->parsingProject->competitor_id,
                    'parsing_id' => $this->id,
                    'created_at' => date('Y-m-d H:i:s'),
                ])
                ->execute();
        }
    }
}