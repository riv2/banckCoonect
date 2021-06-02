<?php

/**
 * @var $this           netis\crud\web\View
 * @var $model ProjectChart
 */

use app\models\pool\ProjectChart;
use onmotion\apexcharts\ApexchartsWidget;

$json = \yii\helpers\Json::encode(Yii::$app->request->get());
$this->registerJs(<<<JS
    window.params = {$json};
    console.log(window.params);
JS
);

echo ApexchartsWidget::widget([
    'type' => 'bar',
    'height' => 400,
    'width' => '100%',
    'chartOptions' => $model->getChartOptions(),
    'series' => $model->generateChartSeries(),
]);
