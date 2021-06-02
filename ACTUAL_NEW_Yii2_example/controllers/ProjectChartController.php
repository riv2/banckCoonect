<?php

namespace app\controllers;

use app\components\crud\controllers\ActiveController;
use app\models\pool\ProjectChart;
use app\models\register\FileExchange;
use app\models\register\Task;
use PhpParser\Node\Scalar\MagicConst\File;
use yii;

class ProjectChartController extends yii\web\Controller
{
    public $modelClass          = 'app\models\pool\ProjectChart';
    public $searchModelClass    = 'app\models\pool\ProjectChart';

    public $layout = 'iframe';

    public function actionIndex(): string
    {
        $model = new ProjectChart();

        $model->load(Yii::$app->request->get());

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
