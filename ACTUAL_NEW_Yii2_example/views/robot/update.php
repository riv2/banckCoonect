<?php

use yii\bootstrap\Html;
use kartik\form\ActiveForm;


/**
 * @var $this netis\crud\web\View
 * @var $model \app\models\reference\Robot
 * @var $controller netis\crud\crud\ActiveController
 * @var $competitors \app\models\reference\Competitor[]
 */

$controller = $this->context;

if (!$model->isNewRecord) {
    $this->title = 'Робот '.$model->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
    $this->params['menu']        = $controller->getMenu($controller->action, $model);
}
?>

<?= netis\crud\web\Alerts::widget() ?>

<?php
$form = ActiveForm::begin([
    'validateOnSubmit'      => true,
    'enableAjaxValidation'  => true,
    'enableClientValidation' => true,
    'method' => 'post',
    'options' => [
        'enctype' => 'multipart/form-data',
    ]
]);
?>

<div class="pull-right">
    <?=  Html::submitButton(
        '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
        ['class' => 'btn btn-success', 'type' => 'submit']
    ) ?>
</div>
<h1> <?= Html::encode($model->name) ?> <small><?=$model->id?></small></h1>

<div class="box">
    <div class="box-body">

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'name')->input('text') ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'status_id')->dropDownList(\app\models\enum\Status::getEnumList()) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'max_projects')->input('number') ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'timeout')->input('number') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'max_connections')->input('number') ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'rate_limit')->input('number') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'retries')->input('number') ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'retry_timeout')->input('number') ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'anticaptcha_key')->input('text') ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'color')->input('text') ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'proxies')->textarea(['rows' => 30]) ?>
                </div>
                <div class="col-xs-12 col-sm-6">
                    <?= $form->field($model, 'user_agents')->textarea(['rows' => 30]) ?>
                </div>
            </div>
</div>


<?php
$this->registerJs("");
$form->end();
?>

