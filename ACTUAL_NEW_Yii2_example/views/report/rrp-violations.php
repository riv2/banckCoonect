<?php
/** @var array $columns */
/** @var array $searchFields */
/** @var array $gridOptions */
/** @var string $gridId */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $layout */
/** @var string $projectExecutionId */
/** @var string $brandId */
/** @var \app\models\document\ProjectExecution $projectExecution */
use app\models\reference\Brand;
use app\widgets\FormBuilder;
use app\widgets\GridView;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\widgets\Pjax;

if ($this->context->layout != 'iframe') {
    echo '<h1><span>Нарушения конкурентов @ '.$projectExecution.'</span></h1>';
}
FormBuilder::registerSelect($this);
echo FormBuilder::registerRelations($this);

$form = ActiveForm::begin([
    'id'                    => 'report-form',
    'method' => 'get',
    'options' => [
        'target'            => 'reports-iframe',
        'enctype'           => 'multipart/form-data',
    ]
]);
?>
<div class="row">
    <div class="col-xs-6">
        <label>Выбрать бренды:</label>
        <?=FormBuilder::renderSelect2($this, Brand::className(), 'brand_id', $brandId, true ) ?>
    </div>
    <div class="col-xs-3 text-left">
        <label>&nbsp;</label><br/>
        <?=Html::hiddenInput('project_execution_id', $projectExecutionId)?>
        <?=Html::submitButton('Применить',[
            'class' => 'btn btn-primary',
            'onclick' => new \yii\web\JsExpression("")
        ])?>
    </div>
    <div class="col-xs-3 text-right">
        <label>&nbsp;</label><br/>
        <?=Html::a('<span class="fa fa-file-excel-o"></span> Экспорт в XLS',[
            '/report/rrp-violations',
            'project_execution_id' => $projectExecutionId,
            'brand_id' => $brandId,
            'export' => 'csv',
            'iframe' => Yii::$app->request->get('iframe',null)],[
            'class' => 'btn btn-info btn-md',
            'target' => '_self',
        ])?>
    </div>
</div>
<p>&nbsp;</p>
<?php ActiveForm::end(); ?>

<?php Pjax::begin(['id' => 'indexPjax']); ?>
<?=GridView::widget(array_merge([
    'id'             => $gridId,
    'dataProvider'   => $dataProvider,
    // this actually renders some widgets and must be called after Pjax::begin()
    'columns'        => $columns,
], $gridOptions));?>

<?php Pjax::end(); ?>
