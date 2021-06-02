<?php

use kartik\form\ActiveForm;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use \app\components\DateTime;

/** @var $model         \app\models\pool\ReportProjectChart */
/** @var $this          \yii\web\View */
/** @var $dataProvider  yii\data\ActiveDataProvider */

$this->title = $model->getSingularNominativeName();

?>

<form class="form form-inline" id="parsing-projects-search">
    <div class="row" style="margin-bottom: 15px;">
        <?php foreach ([
               'project_id'                 => \app\models\reference\Project::className(),
               'competitor_id'              => \app\models\reference\Competitor::className(),
               'brand_id'                   => \app\models\reference\Brand::className(),
               'item_id'                    => \app\models\reference\Item::className(),
    //           'category_id' => \app\models\reference\Category::className(),
           ] as $field => $class): ?>
            <div class="col-sm-2">
<!--                <div>-->
                    <label>
                        <?= $model->getAttributeLabel($field)?>
                    </label>
                    <?= \app\widgets\FormBuilder::renderSelect2(
                        $this,
                        $class,
                        $field,
                        $model->$field,
                        false,
                        0
                    ) ?>
<!--                </div>-->
            </div>
        <?php endforeach; ?>

        <div class="col-sm-2">
            <label>
                <?= $model->getAttributeLabel('series_index')?>
            </label>
            <div>
                <?= Html::dropDownList(
                    'series_index',
                    $model->series_index,
                    \app\models\pool\ReportProjectChart::getSeriesLabels($model->type),
                    [
                        'class' => 'form-control',
                        'style' => 'width:100%',
                    ]
                ) ?>
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 15px;">
        <div class="col-sm-12">
            <?= Html::submitButton('Применить', [
                'class' => 'btn btn-success',
            ])?>
        </div>
        <div class="col-sm-12">
            <?= Html::a(
                'Экспорт',
                \yii\helpers\Url::to([
                    '/report-project-chart/export',
                    'competitor_id' => $model->competitor_id,
                    'brand_id'      => $model->brand_id,
                    'project_id'    => $model->project_id,
                    'item_id'       => $model->item_id,
                    'series_index'  => $model->series_index,
                    'date'          => $model->date,
                    'type'          => $model->type,
                ]),
                [
                    'class' => 'btn btn-success',
                    'target' => '_blank',
                ]
            )?>
        </div>
    </div>
    <?= Html::hiddenInput('type', $model->type) ?>
    <?= Html::hiddenInput('date', $model->date) ?>
</form>

<?= \app\widgets\GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => $model->crudIndexColumns(),
]);
?>
