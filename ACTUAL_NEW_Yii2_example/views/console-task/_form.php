<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\widgets\FormBuilder;
use netis\crud\crud\UpdateAction;

/* @var $this \netis\crud\web\View */
/* @var $model \app\models\reference\ConsoleTask */
/* @var $fields array */
/* @var $relations array */
/* @var $form yii\widgets\ActiveForm */
/* @var $controller netis\crud\crud\ActiveController */
/* @var $action netis\crud\crud\UpdateAction */
/* @var $view \netis\crud\web\View */
/* @var $formOptions array form options, will be merged with defaults */
/* @var $buttons array */
/* @var $formBody string if set, allows to override only the form part */
/* @var $defaultWidth integer default form column width */
/* @var $errorSummaryModels array models passed to form error summary, defaults to $model */

$controller = $this->context;
$action = $controller->action;
$view = $this;

if (!isset($defaultWidth)) {
    $defaultWidth = Yii::$app->request->getIsAjax() ? 12 : 4;
}

if (!isset($buttons)) {
    $buttons = [
        Html::submitButton(
            $model->isNewRecord ? 'Создать' : 'Изменить',
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']
        ),
    ];
}

FormBuilder::registerSelect($this);
?>

    <div class="ar-form">
        <?php $form = isset($form) ? $form : ActiveForm::begin(array_merge([
            'enableAjaxValidation' => !Yii::$app->request->getIsAjax(),
            'validateOnSubmit' => true,
            'options' => [
                'enctype' => 'multipart/form-data',
            ],
        ], isset($formOptions) ? $formOptions : [])); ?>

        <?php if (FormBuilder::hasRequiredFields($model, $fields)):?>
            <p class="note">
                Поля с <span class="required">*</span> обязательны
            </p>
        <?php endif;?>

        <?= $form->errorSummary(!isset($errorSummaryModels) ? $model : $errorSummaryModels); ?>

        <?php if (!empty($fields)): ?>
            <fieldset class="well">

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <?= $form->field($model, 'name') ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <?= $form->field($model, 'start_date')->widget(kartik\daterange\DateRangePicker::className(), [
                                'convertFormat' => true,
                                'pluginOptions' => [
                                    'timePicker' => true,
                                    'locale' => ['format' => 'Y-m-d H:i:s'],
                                    'singleDatePicker' => true,
                                ]
                            ]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <?= $form->field($model, 'is_repeatable')->checkbox() ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <?= $form->field($model, 'repeat_interval') ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <label>
                                <?= $model->getAttributeLabel('console_task_status_id')?>
                            </label>
                            <?= \app\widgets\FormBuilder::renderSelect2(
                                $this,
                                \app\models\enum\ConsoleTaskStatus::className(),
                                \yii\helpers\Html::getInputName($model, 'console_task_status_id'),
                                $model->console_task_status_id,
                                false,
                                0
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <label>
                                <?= $model->getAttributeLabel('console_task_type_id')?>
                            </label>
                            <?= \app\widgets\FormBuilder::renderSelect2(
                                $this,
                                \app\models\enum\ConsoleTaskType::className(),
                                \yii\helpers\Html::getInputName($model, 'console_task_type_id'),
                                $model->console_task_type_id,
                                false,
                                0
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-<?= $defaultWidth ?>">
                        <div class="form-group">
                            <?= $form->field($model, 'params')->textarea() ?>
                        </div>
                    </div>
                </div>
            </fieldset>
        <?php endif; ?>

        <?= $this->render('_relations', [
            'model' => $model,
            'relations' => $relations,
        ], $this->context) ?>

        <div class="form-group form-buttons">
            <?= implode("\n        ", $buttons); ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php
//register modal _after_ ActiveForm. ActiveForm could be initialized outside this view.
//do not render modal for relations handling if request is pjax and content will be inserted in modal.
if (($pjax = Yii::$app->request->getQueryParam('_pjax')) === null || $pjax !== '#relationModal .modal-body') {
    echo FormBuilder::registerRelations($this);
}
if (isset($_GET[UpdateAction::ADD_RELATED_NAME])) {
    $this->registerJs(
        '$("#createRelation-' . $_GET[UpdateAction::ADD_RELATED_NAME] . '").click();'
    );
}
