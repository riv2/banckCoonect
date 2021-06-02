<?php

namespace app\controllers;

use app\components\base\BaseModel;
use app\components\base\Entity;
use app\components\crud\controllers\ActiveController;
use app\components\DateTime;
use app\components\QueryBuilder;
use app\models\enum\ParsingStatus;
use app\models\enum\Status;
use app\models\pool\ParsingError;
use app\models\pool\PriceParsed;
use app\models\reference\ParsingProject;
use app\models\reference\Robot;
use app\models\register\AntiCaptchaTask;
use app\models\register\FileExchange;
use app\models\register\Parsing;
use yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class SiteController  extends Controller
{


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return array_merge(parent::actions(), [
            'download',
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
            'update'        => null,
            'view'          => null,
        ]);
    }

    public function getMenu() {

    }

    public function getBreadcrumbs() {

    }

    /**
     * @return string
     */
    public function actionManual() {

        return $this->render('manual');
    }

    /**
     * @return string
     */
    public function actionIndex() {

        Yii::$app->assetManager->forceCopy = true;

        $swarms = Robot::find()
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE
            ])
            ->select('id,name,color')
            ->asArray()
            ->all();

        $activeParsings = Yii::$app->cache->get('active_parsings_data');
        if (!$activeParsings) {
            $activeParsings = [];
        }

        return $this->render('pricing-dashboard', [
            'swarms' => Json::encode($swarms),
            'activeParsings' => $activeParsings,
        ]);
    }

    /**
     * @param $id
     * @return array
     * @throws \AMQPChannelException
     * @throws \AMQPConnectionException
     * @throws \AMQPQueueException
     */
    public function actionCancelParsing($id) {

        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

        Parsing::cancel($id);

        return ['ok' => $id];
    }

    /**
     * @param $id
     * @return array
     * @throws yii\db\IntegrityException
     */
    public function actionSearchCsvFilterUpload($id) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

        $uploadedFiles = UploadedFile::getInstancesByName('csv-filter');

        foreach ($uploadedFiles as $uploadedFile) {
            $fileTempName = $uploadedFile->tempName;
            $fileName = $uploadedFile->baseName;

            $records = [];

            $handle = fopen($fileTempName, "r");

            $entityClass = Entity::getClassNameById($id);
            /** @var BaseModel $model */
            $model = new $entityClass;

            if ($handle) {
                $first = true;
                $keys = [];
                while (($line = fgets($handle)) !== false) {
                    $row = explode(';', trim($line));
                    if ($first) {
                        $keys = $row;
                        foreach ($keys as $key) {
                            if (!in_array($key, $model->attributes())) {
                                return ['error' => 'Забыли указать названия колонок, либо в ['.$model->getSingularNominativeName().'] нет колонки '.$key];
                            }
                        }

                    } else{
                        $record = [];
                        foreach ($row as $i => $val) {
                            $record[$keys[$i]] = $val;
                        }
                        $records[] = $record;
                    }
                    $first = false;
                }
                fclose($handle);
                @unlink($fileTempName);
            } else {
                return ['error' => "Can't open file"];
            }
            $count = count($records);

            $key = md5($fileTempName);

            BaseModel::setCsvFilterData($key, $fileName, $records);

            return ['success' => true, 'key' => $key, 'fileName' => $fileName, 'count' => $count];
        }

        return ['error' => 'No files uploaded'];
    }

    /**
     * @param $id
     * @return null|string
     */
    public function actionDownload($id) {

        $fileExchange = FileExchange::findOne($id);

        if ($fileExchange) {
            $fileExchange->status_id = Status::STATUS_DISABLED;
            $fileExchange->save();
            return $this->render('download',[
                'filePath' => $fileExchange->file_path,
                'fileName' => $fileExchange->original_file_name,
            ]);
        }
        return null;
    }


    /**
     * @param $id
     * @return null|string
     */
    public function actionFileDismiss($id) {

        $fileExchange = FileExchange::findOne($id);

        if ($fileExchange) {
            @unlink($fileExchange->file_path);
            $fileExchange->status_id = Status::STATUS_DISABLED;
            $fileExchange->save();
        }

        return null;
    }

    /**
     * Создание метрик
     * @return string
     */
    public function actionMetrics()
    {
        $this->layout = false;
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/plain');

        $result = '';

        $activeParsingsIds = Parsing::find()
            ->select('id')
            ->andWhere([
                'status_id' => Status::STATUS_ACTIVE,
                'parsing_status_id' => [ParsingStatus::STATUS_PROCESSING],
            ])
            ->column();

        $query = Parsing::find()
            ->alias('p')
            ->select([
                '*',
                'success_count' => 'ppa.items_count',
                'errors_count' => 'pe.errors_count',
            ])
            ->from([
                'p' => Parsing::find()
                    ->select([
                        'id',
                        'parsing_project_id',
                        'name',
                        'created_at'
                    ])
                    ->andWhere(['id' => $activeParsingsIds])
            ])
            ->leftJoin([
                'ppa' => PriceParsed::find()
                    ->select([
                        'parsing_id',
                        'COUNT(DISTINCT url) items_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->andWhere(['>', 'extracted_at', date('Y-m-d H:i:s', strtotime('1 hour ago'))])
                    ->groupBy('parsing_id')
            ], 'ppa.parsing_id = p.id')
            ->leftJoin([
                'pe' => ParsingError::find()
                    ->select([
                        'parsing_id',
                        'COUNT(*) as errors_count',
                    ])
                    ->andWhere(['parsing_id' => $activeParsingsIds])
                    ->andWhere(['>', 'created_at', date('Y-m-d H:i:s', strtotime('1 hour ago'))])
                    ->groupBy('parsing_id')
            ], 'pe.parsing_id = p.id')
            ->orderBy('p.created_at, p.name');
        
        $parsingsData = [];
        $notificationsForParsings = [];
        $hourAgo = (new DateTime('1 hour ago'));

        /** @var Parsing $parsing */
        foreach ($query->each() as $parsing) {
            $parsingsData[$parsing->id] = [str_replace('"', '\'', $parsing->name), $parsing->success_count, $parsing->errors_count];
            if ($parsing->parsingProject->signals_enabled
                && $parsing->created_at < $hourAgo
                && ($parsing->success_count < $parsing->parsingProject->items_per_hour_available
                    || $parsing->errors_count > $parsing->parsingProject->errors_per_hour_available)) {
                $notificationsForParsings[] = $parsing->name;
            }
        }

        if (count($parsingsData) === 0) {
            return $result;
        }

        $result .= "# HELP pstat_parsing_items_count Количество обработанных урлов в парсинге в час\n"
            . "# TYPE pstat_parsing_items_count gauge\n";
        foreach ($parsingsData as $id => $data) {
            $result .= 'pstat_parsing_items_count{parsing="' . $data[0] . '"} ' . ($data[1] ?: 0) . "\n";
        }

        $result .= "# HELP pstat_parsing_errors_count Количество ошибок в парсинге в час\n"
            . "# TYPE pstat_parsing_errors_count gauge\n";
        foreach ($parsingsData as $name => $data) {
            $result .= 'pstat_parsing_errors_count{parsing="' . $data[0] . '"} ' . ($data[2] ?: 0) . "\n";
        }

        if (count($notificationsForParsings) > 0) {
            $result .= "# HELP pstat_parsing_signal Парсинги с проблемами сбора\n"
                . "# TYPE pstat_parsing_signal gauge\n";
            foreach ($notificationsForParsings as $parsingName) {
                $result .= 'pstat_parsing_signal{parsing="' . str_replace(' ', '_', $parsingName) . '"} 1' . "\n";
            }
            $result .= "# HELP pstat_parsing_notification Уведомление о проблемах со сбором\n"
                . "# TYPE pstat_parsing_notification gauge\n"
                . "pstat_parsing_notification 1\n";
        }

        $antiCaptchaTasksQuery = AntiCaptchaTask::find()
            ->alias('act')
            ->select([
                'p.name',
                'COUNT(act.parsing_id)',
            ])
            ->leftJoin(['p' => Parsing::tableName()], 'p.id = act.parsing_id')
            ->andWhere(['>', 'act.created_at', $hourAgo])
            ->andWhere(['!=', 'p.name', ''])
            ->groupBy(['act.parsing_id', 'p.name'])
            ->asArray();

        if ($antiCaptchaTasksQuery->exists()) {
            $result .= "# HELP pstat_parsing_captcha_count Количество капч в парсинге в час\n"
                . "# TYPE pstat_parsing_captcha_count gauge\n";
            foreach ($antiCaptchaTasksQuery->each() as $data) {
                $result .= 'pstat_parsing_captcha_count{parsing="' . $data['name'] . '"} ' . ($data['count'] ?: 0) . "\n";
            }
        }

        return $result;
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['metrics'],
                        'roles' => ['?']
                    ],
                ],
            ],
        ];
    }



}
