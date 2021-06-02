<?php

namespace app\controllers;

use app\components\base\Entity;
use app\components\QueryBuilder;
use app\components\ReportKpiData;
use app\components\ReportKpiProject;
use app\models\document\ProjectExecution;
use app\models\enum\FileFormat;
use app\models\enum\Source;
use app\models\enum\Status;
use app\models\pool\LogPriceCalculation;
use app\models\pool\PriceParsed;
use app\models\pool\ReportKpi;
use app\models\reference\Competitor;
use app\models\reference\CompetitorItem;
use app\models\reference\FileExchangeSettings;
use app\models\reference\Item;
use app\models\reference\ParsingProject;
use app\models\reference\Project;
use app\models\reference\ProjectItem;
use app\models\register\FileExchange;
use netis\crud\db\ActiveQuery;
use yii;
use yii\data\ArrayDataProvider;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;

class ReportController extends Controller
{

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
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'rrp-violations',
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->request->get('iframe',null)) {
            $this->layout = 'iframe';
        }
        return parent::beforeAction($action);
    }

    public function actionRrpViolationRedirect($item_id, $competitor_id, $lpc_id, $project_execution_id) {
        /** @var LogPriceCalculation $lpc */
        $lpc = LogPriceCalculation::findOne($lpc_id);

        $url = CompetitorItem::find()->andWhere([
            'competitor_id' => $competitor_id,
            'item_id'       => $item_id,
            'status_id'     => Status::STATUS_ACTIVE,
            'source_id'     => Source::SOURCE_WEBSITE
        ])->andWhere([
        ])->select([
            'url'
        ])->scalar();

        if (!$url) {
            try {
                $url = $lpc->priceRefined->priceParsed->url;
            } catch (\Error $e) {
                die('Невозможно отследить УРЛ');
            }
        }

        $this->redirect($url);
    }

    public function actionItemQuickUpdate($id) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $item = Item::findOne($id);
        if (!$item) {
            return null;
        }
        $item->load(Yii::$app->request->get());
        if (!$item->validate()) {
            return $item->errors;
        }
        $item->save();
        return ['id' => $item->id, 'Item' => $item->toArray()];
    }


    public function actionRrpViolations($project_execution_id, $brand_id = null, $export = null) {

        $projectExecution = ProjectExecution::findOne($project_execution_id);

        if ($export) {
            $fileExchangeSettings = FileExchangeSettings::getUserSettings(Entity::ProjectExecution, true, 'exportRrpViolations');
            $fileExchangeSettings->loadDefaultValues();
            $fileExchangeSettings->skip_first_row = true;
            $fileExchangeSettings->file_format_id = FileFormat::TYPE_XLS;
            $fileExchangeSettings->columnsValues  = [
                'id'        => $project_execution_id,
                'brand_id'  => $brand_id
            ];
            $fileExchangeSettings->name = 'Нарушения конкурентов @'.$projectExecution;
            $fileExchangeSettings->save();
            $fileExchangeSettings->createFileTask();
            FileExchange::runNext();
            return $this->redirect(Yii::$app->request->referrer);

        } else {
            $projectExecution->brand_id = $brand_id;
            $report = $projectExecution->exportRrpViolations(false, Yii::$app->request->get('page', 1), Yii::$app->request->get('per-page', 10));

            $provider = new ArrayDataProvider([
                'allModels'     => $report['items'],
                'sort'          => $report['sort'],
                'totalCount'    => $report['count'],
                'pagination'    => [
                    'pageSize' => Yii::$app->request->get('per-page', 10),
                ],
            ]);
            
            foreach ($report['columns'] as $i => $column) {
                if ($column['attribute'] === 'url') {
                    $report['columns'][$i]['format'] = 'raw';
                    $report['columns'][$i]['value'] = function($model) {
                        $url = $model['url'];
                        if ($url) {
                            $pos = mb_strpos($url, '[PARAM]');
                            if ($pos && $pos > -1) {
                                $url = mb_substr($url, 0, mb_strlen($url) - $pos);
                            }
                            return '<a href="'. $url . '" target="_blank">' . $url . '</a>';
                        }
                        return null;
                    };
                }
            }

            return $this->render('rrp-violations',[
                'gridId'        => 'rrp-violations',
                'projectExecutionId' => $project_execution_id,
                'projectExecution' => $projectExecution,
                'brandId'       => $brand_id,
                'dataProvider'  => $provider,
                'columns'       => $report['columns'],
                'layout'        => $this->layout,
                'gridOptions'   => []
            ]);
        }

    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
