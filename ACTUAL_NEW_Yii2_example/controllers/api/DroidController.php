<?php
namespace app\controllers\api;

use app\models\cross\ParsingProjectRegion;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\pool\ParsingProjectProxy;
use app\models\pool\ParsingProjectProxyBan;
use app\models\pool\ProxyParsingProject;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use app\models\register\Error;
use app\models\register\Parsing;
use app\models\register\Proxy;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class DroidController extends Controller
{

    private function checkDroidSwarmExists($id) {
        $caheKey    = 'Robot.id='.$id;
        $droidSwarm = \Yii::$app->cache->get($caheKey);

        if (!$droidSwarm) {
            $droidSwarm = Robot::findOne($id);
        }

        if (!$droidSwarm) {
            /** @var Robot $copyFrom */
            $copyFrom = Robot::find()->andWhere(['status_id' => Status::STATUS_ACTIVE])->limit(1)->one();
            $droidSwarm              = new Robot();
            $droidSwarm->loadDefaultValues();
            $droidSwarm->id                  = $id;
            $droidSwarm->name                = $id;
            $droidSwarm->status_id           = Status::STATUS_DISABLED;
            $droidSwarm->proxies             = $copyFrom->proxies;
            $droidSwarm->user_agents         = $copyFrom->user_agents;
            $droidSwarm->anticaptcha_key     = $copyFrom->anticaptcha_key;
            $droidSwarm->save();
            \Yii::$app->cache->set($caheKey, $droidSwarm, 300);
        }
    }

    /**
     * Получение списка задач
     * @param $id
     * @return null
     */
    public function actionJob($id = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $this->checkDroidSwarmExists($id);

        $parsings = Parsing::find()
            ->alias('p')
            ->andWhere([
                'p.status_id'         => Status::STATUS_ACTIVE,
                'p.parsing_status_id' => [ParsingStatus::STATUS_QUEUED,ParsingStatus::STATUS_PROCESSING],
                'p.robot_id'          => $id,
                'p.is_test'           => false,
                '(' . Parsing::find()
                    ->alias('pr')
                    ->select('COUNT(*)')
                    ->andWhere('pr.parallel_main_id != p.parallel_main_id AND pr.parsing_project_id = p.parsing_project_id AND pr.region_id != p.region_id AND p.priority = pr.priority')
                    ->andWhere(['pr.parsing_status_id' => ParsingStatus::STATUS_PROCESSING])
                    ->andWhere(['pr.status_id' => Status::STATUS_ACTIVE])
                    ->createCommand()
                    ->getRawSql()
                . ')' => 0
            ])
            ->leftJoin(['ppr' => ParsingProjectRegion::tableName()], 'p.parsing_project_id = ppr.parsing_project_id AND ppr.region_id = p.region_id')
            ->orderBy(['priority' => SORT_DESC, 'p.created_at' => SORT_ASC, 'ppr.sort'=> SORT_ASC])
            ->select('p.id as id, p.hash as hash, p.droid_type as droid_type')
            //->limit($limit)
            ->asArray()
            ->all();

        if ($parsings) {
            $jobsUp = [];

            foreach ($parsings as $parsing) {
                if (!isset($jobsUp[$parsing['hash']])) {
                    $jobsUp[$parsing['hash']] = $parsing;
                }
            }

            $jobs = array_values($jobsUp);

            if ($jobs) {
                Parsing::updateAll(
                    [
                        'robot_id' => $id,
                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                        'started_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'parsing_status_id' => ParsingStatus::STATUS_QUEUED,
                        'id'                => ArrayHelper::getColumn($jobs, 'id')
                    ]
                );
                Parsing::updateAll(
                    [
                        'robot_id' => $id,
                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                    ],
                    [
                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                        'id'                => ArrayHelper::getColumn($jobs, 'id')
                    ]
                );
            }
            return ['jobs' => $jobs];
        }
        return ['jobs' => []];
    }

    public function actionJob2($id = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        while (\Yii::$app->cache->get('job-in-process')) {
            sleep(1);
        }
        \Yii::$app->cache->set('job-in-process', true);

        $this->checkDroidSwarmExists($id);

        $parsings = Parsing::find()
            ->alias('p')
            ->andWhere([
                'p.status_id'         => Status::STATUS_ACTIVE,
                'p.parsing_status_id' => [ParsingStatus::STATUS_QUEUED],
//                'p.robot_id'          => $id,
                'p.is_test'           => false
            ])
            ->andWhere(
                'pp.parallel_droids - (' . Parsing::find()
                    ->alias('pr')
                    ->select('COUNT(*)')
                    ->andWhere('pr.parallel_main_id != p.parallel_main_id AND pr.parsing_project_id = p.parsing_project_id AND p.priority = pr.priority')
                    ->andWhere(['pr.parsing_status_id' => ParsingStatus::STATUS_PROCESSING])
                    ->andWhere(['pr.status_id' => Status::STATUS_ACTIVE])
                    ->createCommand()
                    ->getRawSql()
                . ') - (' . Parsing::find()
                    ->alias('pr')
                    ->select('COUNT(*)')
                    ->andWhere('pr.parallel_main_id = p.parallel_main_id AND p.priority = pr.priority')
                    ->andWhere(['pr.parsing_status_id' => ParsingStatus::STATUS_PROCESSING])
                    ->andWhere(['pr.status_id' => Status::STATUS_ACTIVE])
                    ->createCommand()
                    ->getRawSql()
                . ') > 0')
            ->leftJoin(['ppr' => ParsingProjectRegion::tableName()], 'p.parsing_project_id = ppr.parsing_project_id AND ppr.region_id = p.region_id')
            ->leftJoin(['pp' => ParsingProject::tableName()], 'pp.id = p.parsing_project_id')
            ->orderBy(['priority' => SORT_DESC, 'p.created_at' => SORT_ASC, 'ppr.sort'=> SORT_ASC])
            ->select('p.id as id, p.hash as hash, p.droid_type as droid_type')
            ->limit(1)
            ->asArray()
            ->all();

        if ($parsings) {
            $jobsUp = [];

            foreach ($parsings as $parsing) {
                if (!isset($jobsUp[$parsing['hash']])) {
                    $jobsUp[$parsing['hash']] = $parsing;
                }
            }

            $jobs = array_values($jobsUp);

            if ($jobs) {
                Parsing::updateAll(
                    [
                        'robot_id' => $id,
                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
                        'started_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'parsing_status_id' => ParsingStatus::STATUS_QUEUED,
                        'id'                => ArrayHelper::getColumn($jobs, 'id')
                    ]
                );
//                Parsing::updateAll(
//                    [
//                        'robot_id' => $id,
//                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
//                    ],
//                    [
//                        'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
//                        'id'                => ArrayHelper::getColumn($jobs, 'id')
//                    ]
//                );
            }
            \Yii::$app->cache->set('job-in-process', false);
            return ['jobs' => $jobs];
        }
        \Yii::$app->cache->set('job-in-process', false);
        return ['jobs' => []];
    }


    public function actionReleaseParsings($ids = null)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        Parsing::updateAll([
            'parsing_status_id' => ParsingStatus::STATUS_QUEUED,
        ], [
            'id' => explode(',', $ids),
            'parsing_status_id' => ParsingStatus::STATUS_PROCESSING,
        ]);
        return ['success' => true];
    }

    /**
     * Получение информации о парсинге
     * @param $id
     * @return array|null
     */
    public function actionParsing($id, $robotId = null, $mark_as_in_process = false) {
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $parsing = Parsing::findOne($id);
        $parsing->updated_at = date('Y-m-d H:i:s');
//        if ($mark_as_in_process) {
//            $parsing->parsing_status_id = ParsingStatus::STATUS_PROCESSING;
//        }
        $parsing->save();

        if (!$parsing) {
            return null;
        }

        $amqp =\Yii::$app->params['amqp'];
        $host = $_SERVER['SERVER_NAME'];

        return array_merge($parsing->getSettings($robotId),[
            'rabbitmq'  => "amqp://{$amqp['login']}:{$amqp['password']}@{$amqp['host']}:{$amqp['port']}",
        ]);
    }

    /**
     * Освобождение парсинга
     * @param $id
     * @param null $d
     * @return array
     */
    public function actionFree($id, $d = null) {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $request    = \Yii::$app->request;
        $error      = $request->get('error');

        $parsing = Parsing::findOne($id);
        if (!$parsing) {
            return null;
        }
        if ($parsing->parsing_status_id !== ParsingStatus::STATUS_CANCELED) {
            $parsing->assignNewVpn();
            $parsing->parsing_status_id = ParsingStatus::STATUS_QUEUED;
        }
        //$parsing->robot_id = $d;
        $parsing->save();
        ProxyParsingProject::deleteAll(['parsing_id' => $id]);

        if ($error) {
            $parsingError = new ParsingError;
            $parsingError->parsing_id = $id;
            $parsingError->robot_id = $d;
            $parsingError->message = $error;
            $parsingError->save();
        }
        return ['id' => $id];
    }

    /**
     * Возвращает массив из наименнований всех парсингов в цепочке
     * @return array
     */
    public function actionParsingChain($id)
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $parsing = Parsing::findOne($id);
        if (!$parsing) {
            return [];
        }
        return \yii\helpers\ArrayHelper::map(Parsing::find()
            ->select('id, name')
            ->andWhere(['parallel_main_id' => $parsing->parallel_main_id])
            ->orderBy('name')
            ->asArray()
            ->all(), 'id', 'name');

    }

    /**
     * Обновить дату бана прокси у проекта парсинга
     *
     * @param $id
     * @param $proxy
     * @throws \yii\db\Exception
     */
    public function actionProxyBanned($id, $proxy)
    {
        $parsing = Parsing::findOne($id);
        if (!$parsing) {
            return;
        }
        if (!Proxy::find()->andWhere(['id' => $proxy])->exists()) {
            return;
            Proxy::getDb()->createCommand()->insert(Proxy::tableName(), ['id' => $proxy])->execute();
        }

        if (!ParsingProjectProxyBan::updateAll([
            'banned_at' => date('Y-m-d H:i:s')
        ], [
            'parsing_project_id' => $parsing->parsingProject->id,
            'proxy_id' => $proxy,
        ])) {
            ParsingProjectProxyBan::getDb()
                ->createCommand()
                ->insert(ParsingProjectProxyBan::tableName(), [
                    'id' => ParsingProjectProxyBan::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                    'parsing_project_id' => $parsing->parsingProject->id,
                    'proxy_id' => $proxy,
                    'banned_at' => date('Y-m-d H:i:s')
                ])
                ->execute();
        }
    }


    public function actionProxySwitch($id, $proxy = null) {
       //if (strpos('test-', $id) == 0) {
       //      return;
       //}
        $parsing = Parsing::findOne($id);
        if (!$parsing) {
            return;
        }
        ProxyParsingProject::deleteAll(['parsing_id' => $id]);
        if ($proxy && strlen($proxy) <= 256) {
            if (!ParsingProjectProxyBan::updateAll([
                'banned_at' => date('Y-m-d H:i:s')
            ], [
                'parsing_project_id' => $parsing->parsing_project_id,
                'proxy_id' => $proxy,
            ])) {
                ParsingProjectProxyBan::getDb()
                    ->createCommand()
                    ->insert(ParsingProjectProxyBan::tableName(), [
                        'id' => ParsingProjectProxyBan::getDb()->createCommand("select uuid_generate_v4()")->queryScalar(),
                        'parsing_project_id' => $parsing->parsing_project_id,
                        'proxy_id' => $proxy,
                        'banned_at' => date('Y-m-d H:i:s')
                    ])
                    ->execute();
            }
        }

        $proxy = null;
        $parsingProjectProxiesQuery = ParsingProjectProxy::find()
            ->alias('ppp')
            ->andWhere(['ppp.parsing_project_id' => $parsing->parsing_project_id]);
        if ($parsingProjectProxiesQuery->exists()) {
            $proxy = $parsingProjectProxiesQuery
                ->select('ppp.proxy_id')
                ->leftJoin(
                    ['pppb' => ParsingProjectProxyBan::tableName()],
                    'pppb.parsing_project_id = ppp.parsing_project_id AND pppb.proxy_id = ppp.proxy_id'
                )
                ->leftJoin(
                    ['pppe' => ProxyParsingProject::tableName()],
                    'pppb.parsing_project_id = pppe.parsing_project_id AND pppb.proxy_id = pppe.proxy_id'
                )
                ->andWhere('pppe.id IS NULL')
                ->orderBy('pppb.banned_at ASC')
                ->limit(1)
                ->scalar()
            ;
        } else {
            $proxy = Proxy::find()
                ->alias('p')
                ->select('p.id')
                ->leftJoin(
                    ['pppb' => ParsingProjectProxyBan::tableName()],
                    'pppb.proxy_id = p.id AND pppb.parsing_project_id = \'' . $parsing->parsing_project_id . '\''
                )
                ->leftJoin(
                    ['ppp' => ProxyParsingProject::tableName()],
                    'ppp.proxy_id = p.id AND ppp.parsing_project_id = \'' . $parsing->parsing_project_id . '\'',

                )
                ->andWhere('ppp.id IS NULL')
                ->andWhere(['is_public' => true, 'status_id' => 0])
                ->andWhere('until IS NULL OR until > \'' . date('Y-m-d H:i:s') . '\'')
                ->orderBy(new Expression('(CASE WHEN pppb.banned_at IS NULL THEN 1 ELSE 0 END) DESC, pppb.banned_at'))
                ->limit(1)
                ->scalar();
        }

        if ($proxy) {
            ProxyParsingProject::getDb()->createCommand()
                ->insert(
                    ProxyParsingProject::tableName(),
                    [
                        'proxy_id' => $proxy,
                        'parsing_project_id' => $parsing->parsing_project_id,
                        'parsing_id' => $parsing->id,
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                )
                ->execute();
        }
        return $proxy;
    }


    public function actionRegisterError($id = '', $logs = '')
    {
        $id = \Yii::$app->request->post('id');
        $logs = \Yii::$app->request->post('logs');

        $exception = new \Exception('Дроид завершил работу' . $id);
        $data = array_merge([
            'message'           => $exception->getMessage(),
            'name'              => $exception->getMessage(),
            'file'              => $exception->getFile(),
            'line'              => $exception->getLine(),
            'code'              => $exception->getCode(),
            'error_type_id'     => 0,
            'entity_type_id'    => null,
            'entity_row_id'     => null,
            'kind'              => 'Error',
            'backtrace'         => null,
            'info'              => $logs,
        ]);
        $data['hash'] = md5($data['name'].'-'.$data['kind'].'-'.$data['message']);

        $error = new Error();
        $error->setAttributes($data);
        $error->save();
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'     => true,
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index'                 => ['get','post'],
                    'register-error' => ['post'],
                ],
            ],
        ];
    }

    /**
     * debug
     * @return int
     */
    public function actionT()
    {
        $robotId = Robot::find()
            ->alias('r')
            ->orderBy(['c' => SORT_ASC])
            ->groupBy('r.id')
            ->select([
                'robot_id' => 'r.id',
                'c' => Parsing::find()
                    ->andWhere([
                        'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING,ParsingStatus::STATUS_QUEUED],
                        'status_id'         => Status::STATUS_ACTIVE,
                        'robot_id'  => new Expression('r.id')
                    ])
                    ->select('count( distinct parsing_project_id)')

            ])
            ->asArray()
            ->all();
        print_r($robotId);
        return 1;
    }


}