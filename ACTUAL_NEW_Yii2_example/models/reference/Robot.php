<?php
namespace app\models\reference;

use app\components\base\type\Reference;
use app\components\ValidationRules;
use app\models\enum\ParsingStatus;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\ParsingProjectProxy;
use app\models\pool\ParsingProjectProxyBan;
use app\models\pool\ProxyParsingProject;
use app\models\register\Parsing;
use app\models\register\Proxy;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use GuzzleHttp\Client;

/**
 * Class Masks
 * @package app\models\reference
 *
 * @property string id
 *
 * @property string proxies
 * @property string user_agents
 * @property string anticaptcha_key
 *
 * @property int max_projects
 * @property int max_connections
 * @property int rate_limit
 * @property int retry_timeout
 * @property int timeout
 * @property int retries
 *
 *
 * @property Source source
 * 
 */
class Robot extends Reference
{
    protected $client = null;

    const WS_CHANNEL = 'test-parsing';
    const ROBOT_MAIN = 'main';
    const ROBOT_TEST = 'main';

    public static function getAnyRobot() {
        return self::find()->limit(1)->one();
    }
    
    public function getSettings() {
        //'pricing.vseinstrumenti.ru';//php_uname('n');
        return [
            'id'                => $this->id,
            'statusId'          => $this->status_id,
            'maxProjects'       => $this->max_projects,
            'maxThreads'        => $this->max_connections,
            'rateLimit'         => $this->rate_limit,
            'retryTimeout'      => $this->retry_timeout,
            'timeout'           => $this->timeout,
            'retries'           => $this->retries,
            'antiCaptchaKey'    => $this->anticaptcha_key,
            'proxies'           => Proxy::find()
                ->select('id')
                ->andWhere([
                    'status_id' => 0,
                    'is_public' => true,
                ])
                ->column(),
            'userAgents'        => trim($this->user_agents) ? explode("\n", str_replace("\r", "", $this->user_agents)): [],
        ];
    }
    
    public static function prepareProxies($proxies) {
        $readyProxies = [];
        if (!is_array($proxies)) {
            $proxies = trim($proxies) ? explode("\n", str_replace("\r", "", $proxies)) : [];
        }
        
        $client = new Client(['timeout'   => 1.0]);

        foreach ($proxies as $proxy) {
            $proxy = trim($proxy);
            if ($proxy) {
                if (preg_match("/^https?:\/\/[^\/]+?\/.+$/i", $proxy)) {
                    $response = $client->get($proxy);
                    if ($response->getStatusCode() == 200) {
                        $text = $response->getBody()->getContents();
                        $loadedProxies = trim($text) ? explode("\n", str_replace("\r", "", $text)) : [];
                        foreach ($loadedProxies as $loadedProxy) {
                            $readyProxies[] = 'http://' . $loadedProxy;
                        }
                    }
                } else {
                    $readyProxies[] = 'http://' . $proxy;
                }
            }
        }
        return $readyProxies;
    }


    public function start() {
        return $this->restApi('start');
    }

    public function kill() {
        return $this->restApi('kill');
    }

    public function stop() {
        return $this->restApi('stop');
    }

    public function restart() {
        return $this->restApi('restart');
    }

    public function upgrade() {
        return $this->restApi('update');
    }

    public function info() {
        return $this->restApi('info');
    }

    public function restApi($action) {
        if (!$this->client) {
            $this->client = new Client([
                'base_uri'  => 'http://' . $this->id . ':8008',
                'timeout'   => 1.0,
            ]);
        }
        $response = $this->client->request('GET', $action);
        return Json::decode($response->getBody()->getContents(),true);
    }

    /**
     * @inheritdoc
     */
    public static function getSingularNominativeName()
    {
        return 'Хост дроидов';
    }

    /**
     * @inheritdoc
     */
    public static function getPluralNominativeName()
    {
        return 'Хосты дроидов';
    }

    /**
     * @inheritdoc
     */
    public static function crudDeleteEnabled(){
        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
            ValidationRules::ruleRequired('id'),
            ValidationRules::ruleRequired('name'),
            [
                [['name','id','anticaptcha_key'], 'string'],
                [['proxies','user_agents'], 'safe'],
                [['max_projects','max_connections','rate_limit','retry_timeout','timeout','retries'], 'number'],
            ],
            ValidationRules::ruleDateTime('created_at', 'updated_at'),
            ValidationRules::ruleDefault('status_id', Status::STATUS_ACTIVE),
            ValidationRules::ruleDefault('max_projects', 5),
            ValidationRules::ruleDefault('max_connections', 5),
            ValidationRules::ruleDefault('rate_limit', 2000),
            ValidationRules::ruleDefault('retry_timeout', 2000),
            ValidationRules::ruleDefault('timeout', 5000),
            ValidationRules::ruleDefault('retries', 1),
            ValidationRules::ruleEnum('status_id', Status::className())
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
                'max_projects'               => 'Макс. проектов',
                'max_connections'            => 'Одновременно потоков',
                'rate_limit'                 => 'Промежуток между запросами (мс.)',
                'retry_timeout'              => 'Промежуток между попытками (мс.)',
                'timeout'                    => 'Таймаут запроса (мс.)',
                'retries'                    => 'Доп. попыток',
                'proxies'                    => 'Прокси',
                'user_agents'                => 'Юзер агенты',
                'anticaptcha_key'            => 'Ключ Антикапчи'
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function crudIndexColumns()
    {
        return array_merge(
            parent::crudIndexColumns(),
            [
                'id',
                'name',
                'status',
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public static function relations() {
        return array_merge(parent::relations(),[

        ]);
    }

    /**
     * @inheritdoc
     */
    public function crudIndexSearchRelations()
    {
        return array_merge(
            parent::crudIndexSearchRelations(), 
            [
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        self::updateAll(['proxies' => '']);

        if (isset($changedAttributes['proxies'])) {
            $old = array_map('trim', explode("\n", trim($changedAttributes['proxies'])));
            $new = array_map('trim', explode("\n", trim($this->proxies)));
            $proxiesToDelete = array_diff($old, $new);
            ParsingProjectProxyBan::deleteAll(['proxy_id' => $proxiesToDelete]);
            ProxyParsingProject::deleteAll(['proxy_id' => $proxiesToDelete]);
            ParsingProjectProxy::deleteAll(['proxy_id' => $proxiesToDelete]);
            Proxy::deleteAll(['id' => $proxiesToDelete]);

            $proxiesToInsert = [];
            foreach ($new as $proxy) {
                if (strlen(trim($proxy)) > 0 && !Proxy::find()->andWhere(['id' => trim($proxy)])->exists()) {
                    $proxiesToInsert[] = ['id' => trim($proxy), 'is_public' => true];
                }
            }
            if (count($proxiesToInsert) > 0) {
                Proxy::getDb()
                    ->createCommand()
                    ->batchInsert(
                        Proxy::tableName(),
                        array_keys($proxiesToInsert[0]),
                        $proxiesToInsert
                    )->execute();
            }

            self::updateAll(['proxies' => '']);
        }
        parent::afterSave($insert, $changedAttributes);
    }

    public function afterFind()
    {
        $this->proxies = implode("\n", Proxy::find()
            ->select('id')
            ->andWhere([
                'status_id' => 0,
                'is_public' => true,
            ])
            ->column()
        );
        parent::afterFind();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSource() {
        return $this->hasOne(Source::className(), ['id' => 'source_id']);
    }


    /**
     * @param $parsingId
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public static function cancelParsing($parsingId) {
        Parsing::cancel($parsingId);
    }


    /**
     * @param $queueName
     * @param $items
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPExchangeException
     * @throws \AMQPQueueException
     */
    public static function sendTaskTo($queueName, $items) {

        $connection = new \AMQPConnection(Yii::$app->params['amqp']);
        $connection->connect();
        $channel = new \AMQPChannel($connection);
        $exchangeName = getenv('RABBIT_PARSING_EXCHANGE');
        $exchange = new \AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType('topic');
        $exchange->setFlags(\AMQP_DURABLE);
        $exchange->declareExchange();
        $queue = new \AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->declareQueue();
        $queueRoute = str_ireplace('_','.', $queueName);
        
        $queue->bind($exchangeName, $queueRoute);

        foreach ($items as $item) {
            $exchange->publish(Json::encode($item), $queueRoute, AMQP_NOPARAM, array('delivery_mode' => AMQP_DURABLE));
        }
        $connection->disconnect();
    }
}