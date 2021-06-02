<?php

use app\widgets\FormBuilder;
use kartik\form\ActiveForm;
use kartik\sortable\Sortable;
use app\widgets\FileExchangeWidget\FileExchangeAsset;
use yii\bootstrap\Html;
use yii\helpers\Json;

/**
 * @var \yii\web\View $this
 * @var string $widgetId
 * @var \app\components\base\BaseModel $model
 * @var array $presetColumns
 * @var array $columns
 * @var array $search
 * @var array $excludeColumns
 * @var \app\models\reference\FileExchangeSettings $fileExchangeSettings
 */

FormBuilder::registerRelations($this);
FormBuilder::registerSelect($this);
FileExchangeAsset::register($this);

\yii\widgets\Pjax::begin(['id' => 'pjax-export']);

$columnsOrder   = Json::encode($fileExchangeSettings->columnsOrder);
$columnsValues  = Json::encode($fileExchangeSettings->columnsValues);
$columnsExcluded  = Json::encode($fileExchangeSettings->excludeColumns);
$columnsNames  = Json::encode($model->attributeLabels());

$this->registerJs("$('#$widgetId-form').fileExchangeWidget({columnsValues:$columnsValues, columnsOrder:$columnsOrder, columnsExcluded:$columnsExcluded, columnsNames: $columnsNames, isExport: true});");

$sortableItems  = [];

$form = isset($form) ? $form : ActiveForm::begin([
    'id'    => "$widgetId-form",
    'enableAjaxValidation'  => false,
    'validateOnSubmit'      => true,
    'method' => 'post',
    'options' => [
        'data-pjax' => true,
        'enctype' => 'multipart/form-data',
    ],
]);


foreach ($columns as $columnName => $columnConfig) {
    if (is_numeric($columnName) && is_string($columnConfig)) {
        $columnName = $columnConfig;
    }
    $sortableItems[] = [
        'content' => '<label>'.$model->getAttributeLabel($columnName).'</label><br/><a href="#" class="btn btn-default btn-xs import-widget-preset" data-column="'.$columnName.'" title="Исключить"><span class="glyphicon glyphicon-remove"></span></a>'.$columnName,
        'options' => [
            'data-column' => $columnName,
            'class'       => ( in_array($columnName, $excludeColumns)) ? 'import-widget-column hidden-disabled' : 'import-widget-column',
        ]
    ];
}

?>

    <?= $form->errorSummary(!isset($errorSummaryModels) ? $model : $errorSummaryModels); ?>

    <?php
    if (!empty($search) && is_array($search)) { ?>
        <div class="box">
            <div class="box-header with-border">
                Примененные фильтры
            </div>
            <div class="box-body">
                <?php
                foreach ($search as $columnName => $value) {
                    echo yii\helpers\Html::input('text', $model->formName()."[$columnName]", is_array($value)?join(',',$value):$value, [
                        'class' => 'import-widget-preset-column',
                        'data-column' => $columnName
                    ]);
                }
                ?>
            </div>
        </div>
    <?php } ?>

    <div class="box">
        <div class="box-header with-border">
            Исключенные колонки
        </div>
        <div class="box-body import-widget-excluded-columns" style="padding: 20px;">
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            Экспортируемые колонки
        </div>
        <div class="box-body">
            <?= Sortable::widget([
                'type'  =>'grid',
                'options' => [
                    'class' => 'import-widget-sortable'
                ],
                'items' => $sortableItems
            ]);?>

            <?= $form->field($fileExchangeSettings, 'skip_first_row')->checkbox([
                'label' => 'Добавить строку с названиями колонок'
            ]) ?>
        </div>
    </div>



    <div class="box box-success">
        <div class="box-header with-border">
            Куда экспортировать
        </div>
        <div class="box-body">
            <div class="col-sm-4">
                <?=$form->field($fileExchangeSettings, 'file_format_id')
                    ->radioList([
                        \app\models\enum\FileFormat::TYPE_CSV => '<i class="fa fa-file-text-o" aria-hidden="true"></i> CSV',
                        \app\models\enum\FileFormat::TYPE_XLS => '<i class="fa fa-file-excel-o" aria-hidden="true"></i> XLS',
                        \app\models\enum\FileFormat::TYPE_JSON => '<i class="fa fa-file-code-o" aria-hidden="true"></i> JSON',
                    ]);
                ?>
            </div>
            <div class="col-sm-4">
                <?=$form->field($fileExchangeSettings, 'encoding')
                    ->radioList([
                        'UTF-8'         => 'UTF-8',
                        'Windows-1251'  => 'Windows-1251',
                    ]);
                ?>
            </div>
        </div>
    </div>

    <?php

    if (count($search) == 0) {
        echo '<div class="alert alert-warning" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-warning-sign"></span> Не применен ни один фильтр! Экспорт целого справочника может быть долгим. </div>';
    }

    ?>
    <?=  Html::submitButton(
        '<span class="glyphicon glyphicon-export"></span> Экспортировать',
        ['class' => 'btn btn-success btn-export']
    ) ?>

    <?php
        echo Html::hiddenInput('import_settings_id', $fileExchangeSettings->id);
        echo $form->field($fileExchangeSettings, 'exclude_columns')->hiddenInput()->label('');
        echo $form->field($fileExchangeSettings, 'preset_columns')->hiddenInput()->label('');
        echo $form->field($fileExchangeSettings, 'columns_order')->hiddenInput()->label('');
        echo $form->field($fileExchangeSettings, 'entity_id')->hiddenInput()->label('');
        echo $form->field($fileExchangeSettings, 'is_export')->hiddenInput(['value' => 1])->label('');
    ?>

    <?php ActiveForm::end(); ?>

<?php \yii\widgets\Pjax::end() ?>
