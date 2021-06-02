<?php

/**
 * @var $this           netis\crud\web\View
 * @var $dataProvider   yii\data\ActiveDataProvider
 * @var $columns        array
 * @var $searchModel    \app\models\register\Proxy
 */

use app\widgets\FormBuilder;
use app\widgets\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>

<form class="form form-inline" id="parsing-projects-search">
    <div class="row">
        <div class="col-xs-4" style="margin-bottom: 10px;">
            <?= \app\widgets\FormBuilder::renderSelect2($this,
                \app\models\reference\ParsingProject::className(),
                'parsing-projects',
                Yii::$app->request->get('parsing-projects'),
                true
            ) ?>
        </div>
        <div class="col-xs-5 text-left">
            <?= Html::submitButton('Применить',['class' => 'btn btn-primary']); ?>
            <?= Html::a('Добавить элементы', '#', ['class' => 'btn btn-success btn-add']); ?>
            <?= Html::a('Сменить публикацию', '#', ['class' => 'btn btn-success btn-public-all']); ?>
            <?= Html::a('Удалить выбранные элементы', '#', ['class' => 'btn btn-danger btn-remove-all']); ?>
        </div>
    </div>
</form>

<?= netis\crud\web\Alerts::widget(); ?>

<?= GridView::widget([
    'dataProvider'   => $dataProvider,
    'filterModel'    => $searchModel,
    'columns'        => array_merge([
        '__actions' => [
            'class'         => \app\widgets\GridView\columns\ActionColumn::className(),
            'headerOptions' => ['class' => 'column-action'],
        ],
        '__checkbox' => [
            'class'         => 'yii\grid\CheckboxColumn',
            'headerOptions' => ['class' => 'column-checkbox'],
            'multiple'      => true,
        ]
    ], $columns),
]);?>

<?php

Modal::begin([
    'id' => 'addProxies',
    'header' => '<h2>Новые прокси</h2>',
]);
?>
<form method="post">
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-12">
            <?= Html::textarea('proxy-list', Yii::$app->request->get('proxy-list'), [
                'class'       => 'form-control',
                'placeholder' => 'Вставьте сюда прокси-сервера',
                'rows' => '8'
            ]); ?>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-12">
            <?= Html::label('Проекты парсинга')?>
            <?= \app\widgets\FormBuilder::renderSelect2($this,
                \app\models\reference\ParsingProject::className(),
                'proxy-parsing-projects',
                Yii::$app->request->get('proxy-parsing-projects'),
                true
            ) ?>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <div class="col-xs-12">
            <?= Html::label('Действительны до')?>
            <?= kartik\daterange\DateRangePicker::widget([
                'name' => 'proxy-until',
                'convertFormat' => true,
                'pluginOptions' => [
                    'timePicker' => false,
                    'singleDatePicker' => true,
                ]
            ]) ?>
        </div>
    </div>
    <?= Html::hiddenInput(\Yii::$app->getRequest()->csrfParam, \Yii::$app->getRequest()->getCsrfToken(), []) ?>

    <?= Html::submitButton('Применить',['class' => 'btn btn-primary']);?>
</form>

<?php
Modal::end();
?>

<?php
$this->registerJs(<<<JS
    $('.btn-remove-all').click(function() {
        var elements = [];
        $('table tbody tr input[name="selection[]"]:checked').each(function (i, el) {
            elements.push(el.value.trim());
        });
        if (confirm("Вы действительно хотите удалить эти элементы?\\n\\n" + elements.join("\\n"))) {
            $.ajax({
                url: '/proxy/delete-all',
                data: {
                    ids: elements.join(','),
                },
                async: false,
            });
            $('#parsing-projects-search').submit();
        }
    });

    $('.btn-add').click(function () {
        $('#addProxies').modal('show');
    });
    
    $('.btn-public-all').click(function() {
        var elements = [];
        $('table tbody tr input[name="selection[]"]:checked').each(function (i, el) {
            elements.push(el.value.trim());
        });
        if (confirm("Вы действительно хотите сменить публикацию этих элементов?\\n\\n" + elements.join("\\n"))) {
            $.ajax({
                url: '/proxy/public-all',
                data: {
                    ids: elements.join(','),
                },
                async: false,
            });
            $('#parsing-projects-search').submit();
        }
    });
JS
);

?>