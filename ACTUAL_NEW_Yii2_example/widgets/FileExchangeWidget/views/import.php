<?php

use app\widgets\FormBuilder;
use app\widgets\FileExchangeWidget\FileExchangeWidget;
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
 * @var array $excludeColumns
 * @var \app\models\reference\FileExchangeSettings $fileExchangeSettings
 */

FormBuilder::registerRelations($this);
FormBuilder::registerSelect($this);
FileExchangeAsset::register($this);

$columnsOrder   = Json::encode($fileExchangeSettings->columnsOrder);
$columnsValues  = Json::encode($fileExchangeSettings->columnsValues);
$columnsExcluded  = Json::encode($fileExchangeSettings->excludeColumns);
$columnsNames  = Json::encode($model->attributeLabels());

$this->registerJs("$('#$widgetId-form').fileExchangeWidget({columnsValues:$columnsValues, columnsOrder:$columnsOrder, columnsExcluded:$columnsExcluded, columnsNames: $columnsNames});");


$sortableItems  = [];

$form = isset($form) ? $form : ActiveForm::begin([
    'id'    => "$widgetId-form",
    'enableAjaxValidation' => false,
    'validateOnSubmit' => false,
    'method' => 'post',
    'options' => [
        'data-pjax' => true,
        'enctype' => 'multipart/form-data',
    ],
]);
?>

    <?= $form->errorSummary(!isset($errorSummaryModels) ? $model : $errorSummaryModels); ?>


    <div class="box">
        <div class="box-header with-border">
            Значения по умолчанию
        </div>
        <div class="box-body import-widget-excluded-columns" style="padding: 20px;">
        </div>
    </div>


    <div class="box">
        <div class="box-header with-border">
            Предопределенные значения
        </div>
        <div class="box-body">
            <?php
            $importColumns2 = array_merge([], $columns);

            if (!empty($presetColumns)) {
                foreach ($presetColumns as $columnName) {
                    if (isset($importColumns2[$columnName]) && !in_array($columnName, $excludeColumns)) {
                        $column = $importColumns2[$columnName];
                        unset($importColumns2[$columnName]);
                        echo FileExchangeWidget::columnPresetTemplate($form, $columnName, $column);
                    }
                }
            }

            if (!empty($excludeColumns)) {
                foreach ($excludeColumns as $columnName) {
                    if (isset($importColumns2[$columnName])) {
                        $column = $importColumns2[$columnName];
                        unset($importColumns2[$columnName]);
                        echo FileExchangeWidget::columnPresetTemplate($form, $columnName, $column, true);
                    }
                }
            }

            if (!empty($importColumns2)) {
                foreach ($importColumns2 as $columnName => $column) {
                    echo FileExchangeWidget::columnPresetTemplate($form, $columnName, $column, true);
                }
            }

            foreach ($fileExchangeSettings->columnsOrder as $columnName) {
                if (isset($columns[$columnName])) {
                    $columnConfig = $columns[$columnName];
                    $sortableItems[] = [
                        'content' => '<label>'.$model->getAttributeLabel($columnName).'</label><br/><a href="#" class="btn btn-default btn-xs import-widget-preset" data-column="'.$columnName.'" title="Задать значение для всех"><span class="glyphicon glyphicon-arrow-up"></span></a>'.$columnName,
                        'options' => [
                            'data-column' => $columnName,
                            'class'       => (in_array($columnName, $presetColumns) || in_array($columnName, $excludeColumns)) ? 'import-widget-column hidden-disabled' : 'import-widget-column',
                        ]
                    ];
                    unset($columns[$columnName]);
                }
            }

            foreach ($columns as $columnName => $columnConfig) {
                $sortableItems[] = [
                    'content' => '<label>'.$model->getAttributeLabel($columnName).'</label><br/><a href="#" class="btn btn-default btn-xs import-widget-preset" data-column="'.$columnName.'" title="Задать значение для всех"><span class="glyphicon glyphicon-arrow-up"></span></a>'.$columnName,
                    'options' => [
                        'data-column' => $columnName,
                        'class'       => (in_array($columnName, $presetColumns) || in_array($columnName, $excludeColumns)) ? 'import-widget-column hidden-disabled' : 'import-widget-column',
                    ]
                ];
            }
            ?>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            Настройка колонок
        </div>
        <div class="box-body">
            <?= $form->field($fileExchangeSettings, 'skip_first_row')->checkbox() ?>
            <?= $form->field($fileExchangeSettings, 'auto_mapping')->checkbox([
                'inputOptions' => !$fileExchangeSettings->skip_first_row ? ['disabled' => 'disabled'] : []
            ]) ?>
            <label>Порядок колонок:</label>
            <?= Sortable::widget([
                'type'  =>'grid',
                'options' => [
                    'class' => 'import-widget-sortable'
                ],
                'items' => $sortableItems
            ]);?>
        </div>
    </div>

    <div class="box box-success">
        <div class="box-header with-border">
            Источник данных
        </div>
        <div class="box-body">
            <div class="col-sm-2">
                <?=$form->field($fileExchangeSettings, 'data_source')
                    ->radioList([
                        'file'      => '<i class="fa fa-file-text-o" aria-hidden="true"></i> Файл',
                        'content'   => '<i class="fa fa-font" aria-hidden="true"></i> Текст',
                    ]);
                ?>
            </div>
            <div class="col-sm-2">
                <?=$form->field($fileExchangeSettings, 'encoding')
                    ->radioList([
                        'Windows-1251'  => 'Windows-1251',
                        'UTF-8'         => 'UTF-8',
                        'Windows-1252'  => 'ANSI',
                    ]);
                ?>
            </div>
            <div class="col-sm-2">
                <?=$form->field($fileExchangeSettings, 'file_format_id')
                    ->radioList([
                        \app\models\enum\FileFormat::TYPE_CSV => '<i class="fa fa-file-text-o" aria-hidden="true"></i> CSV',
                        //                \app\models\enum\FileFormat::TYPE_XLS => '<i class="fa fa-file-excel-o" aria-hidden="true"></i> XLS',
                        \app\models\enum\FileFormat::TYPE_JSON => '<i class="fa fa-file-code-o" aria-hidden="true"></i> JSON',
                    ]);
                ?>
            </div>
            <div class="col-sm-6 import-widget-file">
                <?=
                $form->field($fileExchangeSettings, 'file')->fileInput();
                ?>
            </div>
            <div class="col-sm-6 import-widget-content">
                <?=
                $form->field($fileExchangeSettings, 'content')->textarea([
                    'rows' => 15
                ]);
                ?>
            </div>
        </div>
    </div>


    <?=  Html::submitButton(
        '<span class="glyphicon glyphicon-import"></span> Импортировать',
        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
    ) ?>

<?php
    echo Html::hiddenInput('import_settings_id', $fileExchangeSettings->id);
    echo $form->field($fileExchangeSettings, 'exclude_columns')->hiddenInput()->label('');
    echo $form->field($fileExchangeSettings, 'preset_columns')->hiddenInput()->label('');
    echo $form->field($fileExchangeSettings, 'columns_order')->hiddenInput()->label('');
    echo $form->field($fileExchangeSettings, 'entity_id')->hiddenInput()->label('');
?>
<?php ActiveForm::end(); ?>
