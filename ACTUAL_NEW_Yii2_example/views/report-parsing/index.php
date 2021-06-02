<?php

use kartik\form\ActiveForm;
use yii\helpers\Html;
use \app\components\DateTime;

/** @var $dataProvider  \yii\data\ArrayDataProvider */
/** @var $competitorMetrics  array */
/** @var $model         \app\models\pool\ReportParsing */
/** @var $this          \yii\web\View */

$this->title = $model->getSingularNominativeName();

$this->registerCss(<<<CSS
    .panel-heading > .col-xs-1 {
        padding: 10px 0;
    }
    #search-parsings-form > form > .col-xs-2, #search-parsings-form > form > .col-xs-1 {
        padding: 24px 0 0;
    }
    #accordion tr > td:not(:first-child) {
        padding: 18px 8px 0;
    }
    #accordion table tr > th {
        border: none;
    }
CSS
);

?>

<div class="row" id="search-parsings-form">
    <?php $form = kartik\form\ActiveForm::begin(['method' => 'post']); ?>
    <div class="col-xs-3">
        <?= Html::label($model->getAttributeLabel('date_interval'))?>
        <?= kartik\daterange\DateRangePicker::widget([
            'name' => 'date_interval',
            'value' => $model->date_interval,
            'convertFormat' => true,
            'pluginOptions' => [
                'locale' => ['format' => \app\components\DateTime::DB_DATETIME_FORMAT],
            ]
        ]) ?>
    </div>
    <div class="col-xs-2">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-xs-3"></div>

    <?php ActiveForm::end(); ?>
</div>

<table class="table" id="accordion">
    <tr>
        <th></th>
        <th>Всего</th>
        <th>Страниц</th>
        <th>В наличии</th>
        <th>Ошибок</th>
    </tr>
    <?php foreach ($dataProvider->getModels() as $competitor => $parsings): ?>
        <?php
//            $startDate = min(array_map(function ($p) {return $p['created_at'];}, $parsings));
//            $finishDate = null;
//            foreach ($parsings as $parsing) {
//                if (!$parsing['finished_at'] && $parsing['parsing_status_id'] !== \app\models\enum\ParsingStatus::STATUS_CANCELED) {
//                    break;
//                } else if ($parsing['finished_at'] > $finishDate) {
//                    $finishDate = $parsing['finished_at'];
//                }
//            }
//            $isSameDay = $finishDate
//                ? ((new DateTime($startDate))->format('Ymd') === (new DateTime($finishDate))->format('Ymd'))
//                : false;
        ?>
        <tr>
            <td>
                <a data-toggle="collapse" href="javascript:void(0)">
                    <h4><?= $competitor ?>&nbsp;
<!--                        <span>(--><?//= $isSameDay
//                                ? (new DateTime($startDate))->format('H:i:s')
//                                : $startDate
//                            ?><!----><?//= $finishDate
//                                ? (' - ' . ($isSameDay
//                                        ? (new DateTime($finishDate))->format('H:i:s')
//                                        : $finishDate)
//                                ) : ' - В процессе' ?><!--)</span>-->
                    </h4>
                </a>
            </td>
            <td><?= $competitorMetrics[$competitor]['global_count'] ?></td>
            <td><?= $competitorMetrics[$competitor]['page_count'] ?></td>
            <td><?= $competitorMetrics[$competitor]['in_stock_count'] ?></td>
            <td><?= $competitorMetrics[$competitor]['errors_count'] ?></td>
        </tr>
        <tr class="collapse">
            <td colspan="7">
                <table class="table">
                    <tbody>
                    <tr>
                        <th></th>
                        <th>Дата старта</th>
                        <th>Дата окончания</th>
                        <th>Всего</th>
                        <th>Страниц</th>
                        <th>В наличии</th>
                        <th>Ошибок</th>
                    </tr>
                    <?php
                    $lastDate = null;
                    foreach ($parsings as $parsing): ?>
                        <?php
                        $currentDay = DateTime::createFromFormat(
                            DateTime::DB_DATETIME_FORMAT,
                            $parsing['created_at']
                        );
                        ?>
                        <tr
                            <?php
                            if (!$lastDate || $lastDate->format('Ymd') !== $currentDay->format('Ymd')) {
                                echo 'style="border-top:2px solid darkviolet;"';
                                $lastDate = $currentDay;
                            }
                            ?>>
                            <td><?= \yii\helpers\Html::a(
                                    $parsing['name'],
                                    ['crud-parsing/update', 'id' => $parsing['id']],
                                    ['target' => '_blank']
                                ) ?></td>
                            <td><?= $parsing['created_at'] ?></td>
                            <td><?= $parsing['parsing_status_id'] === \app\models\enum\ParsingStatus::STATUS_CANCELED
                                    ? ($parsing['updated_at'] . ' (отменён)')
                                    : $parsing['finished_at'] ?></td>
                            <td><?= $parsing['global_count'] ?></td>
                            <td><?= $parsing['page_count'] ?></td>
                            <td><?= $parsing['in_stock_count'] ?></td>
                            <td><?= $parsing['errors_count'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<?php

$this->registerJs(<<<JS
    $("a[data-toggle='collapse']").click(function(){
        console.log($(this).closest('tr').next());
        $(this).closest('tr').next().collapse('toggle');
    });
JS
);