<?php

use app\models\reference\Competitor;
use kartik\form\ActiveForm;
use \app\widgets\ListWidget\ListWidget;
use yii\bootstrap\Html;

/** @var $this \netis\crud\web\View */
/** @var Competitor $model */
/** @var $form yii\widgets\ActiveForm */
/** @var $controller netis\crud\crud\ActiveController */
/** @var $action netis\crud\crud\UpdateAction */
/** @var $view \netis\crud\web\View */
/** @var $errorSummaryModels array models passed to form error summary, defaults to $model */

$controller = $this->context;
$action = $controller->action;
$view = $this;

?>

<div class="box">
    <div class="box-body">

        <?php
        $form = ActiveForm::begin([
            'validateOnSubmit'      => true,
            'enableAjaxValidation'  => true,
            'enableClientValidation' => true,
            'method' => 'post',
            'options' => [
                'enctype' => 'multipart/form-data',
            ]
        ])
        ?>

        <div class="row">
            <div class="col-xs-12"><?= $form->field($model, 'name')->input('text') ?></div>
        </div>
        <div class="row">
            <div class="col-xs-12"><?= $form->field($model, 'is_marketplace')->checkbox() ?></div>
        </div>
        <div class="row">
            <div class="col-xs-12"><?= $form->field($model, 'priceLifetime')->input('text') ?></div>
        </div>
        <div class="row margin-bottom">
            <div class="col-sm-12">
                <?=  Html::submitButton(
                    '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']
                ) ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-lg-4">
                <?=ListWidget::widget([
                    'title'         => 'Названия',
                    'items'         => $model->competitorShopNames,
                    'modelClass'    => \app\models\reference\CompetitorShopName::className()
                ])?>
            </div>
            <div class="col-md-6 col-lg-4">
                <?=ListWidget::widget([
                    'title'         => 'YM ID',
                    'items'         => $model->competitorShopIndexes,
                    'modelClass'    => \app\models\reference\CompetitorShopIndex::className(),
                    'validateRegexp'=> '^[0-9]+$'
                ])?>
            </div>
            <div class="col-md-6 col-lg-4">
                <?=ListWidget::widget([
                    'title'         => 'Домены',
                    'items'         => $model->competitorShopDomains,
                    'modelClass'    => \app\models\reference\CompetitorShopDomain::className(),
                    'validateRegexp'=> '^(.*?)\..{2,10}$'
                ])?>
            </div>
        </div>



        <?php
        $form->end();
        ?>
    </div>
</div>