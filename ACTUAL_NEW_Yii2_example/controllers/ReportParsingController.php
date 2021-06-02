<?php
namespace app\controllers;

use app\components\BaseController;
use app\models\pool\ReportParsing;

/**
 * Контроллер отчета парсингов по конкурентам
 */
class ReportParsingController extends BaseController
{
    public $modelClass          = 'app\models\pool\ReportParsing';
    public $searchModelClass    = 'app\models\pool\ReportParsing';

    public function actionIndex()
    {
        /** @var $model ReportParsing */
        $model = new $this->modelClass();

        if (\Yii::$app->request->post('date_interval')) {
            $model->date_interval = \Yii::$app->request->post('date_interval');
        }

        $report = $model->generateReport();

        return $this->render('index', [
            'model' => $model,
            'dataProvider' => $report['dataProvider'],
            'competitorMetrics' => $report['competitorMetrics'],
        ]);
    }
}