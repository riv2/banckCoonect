<?php

use app\widgets\FormBuilder;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model netis\crud\db\ActiveSearchInterface */
/* @var $fields array */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $formBody string if set, allows to override only the form part */

FormBuilder::registerSelect($this);
echo FormBuilder::registerRelations($this);

// split fields into four columns
$sourceFields = $fields;
$columnsNumber = 5;
$size = ceil(count($sourceFields) / (double)$columnsNumber);
$fields = [];
for ($i = 0; $i < $columnsNumber; $i++) {
    $fields[] = array_slice($sourceFields, $i * $size, $size);
}

$visible = false;//array_filter($model->getAttributes()) !== [];
$formId = uniqid();
?>

<div id="advancedSearch" class="collapse <?= $visible ? 'in' : '' ?> ar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => $formId,
    ]); ?>

    <fieldset>
        <?= isset($formBody) ? $formBody : FormBuilder::renderRow($form, $fields, 10); ?>
    </fieldset>

    <div class="form-group">
        <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary']) ?>
        <?= Html::input('reset', 'btnReset', 'Сброс',[
            'class'     => 'btn btn-default',
            'id'        => 'btnReset',
            'onclick'   => "$('#$formId input[type=text], #$formId input[type=number]').val(''); $('#$formId input[type=checkbox], #$formId input[type=radio]').prop('checked', false); return false;"
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
