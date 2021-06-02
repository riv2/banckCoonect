<?php

use app\models\reference\Masks;
use yii\bootstrap\Html;
use app\models\cross\ParsingProjectRegion;
use app\models\enum\Source;
use app\widgets\FormBuilder;
use kartik\form\ActiveForm;
use \app\widgets\ListWidget\ListWidget;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/**
 * @var $controller netis\crud\crud\ActiveController
 */
/** @var $this \netis\crud\web\View */
/** @var \app\models\reference\ParsingProject $model */
/** @var $form yii\widgets\ActiveForm */

$controller = $this->context;

if (!$model->isNewRecord) {
    $this->title = $model->__toString();
}

if ($controller instanceof \yii\base\Controller) {
    $this->params['breadcrumbs'] = $controller->getBreadcrumbs($controller->action, $model);
    $this->params['menu']        = $controller->getMenu($controller->action, $model);
}

/** @var \app\models\reference\Robot $anyRobot */
$anyRobot = \app\models\reference\Robot::getAnyRobot();
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
]) /**
 *                         <div class="form-group">
<label>Проекты</label>
<?=FormBuilder::renderSelect2($this, \app\models\reference\Project::className(), 'ParsingProjectProject', $model->getProjects()->select('project_id')->column(), true, 0) ?>
</div>
 */
?>

    <div class="pull-right">
        <?= $model->isNewRecord ? null : Html::a(
            '<span class="fa fa-eye"></span> Посмотреть список урлов',
            [
                '/parsing-project/urls',
                'id'        => $model->id,
            ],
            [
                'class' => 'btn btn-default',
                'target' => '_blank'
            ]
        ) ?>
        <?= $model->isNewRecord ? null : Html::a(
            '<span class="fa fa-play"></span> Собрать цены',
            [
                '/parsing-project/execute',
                'id'        => $model->id,
            ],
            ['class' => 'btn btn-primary']
        ) ?>
        <?=  Html::submitButton(
            '<span class="glyphicon glyphicon-floppy-disk"></span> Сохранить',
            ['class' => 'btn btn-success']
        ) ?>
    </div>

    <?php if ($model->isNewRecord): ?>
        <h1>Новый проект парсинга</h1>
    <?php else: ?>
        <h1>Проект парсинга: <span><?= Html::encode($this->title) ?></span></h1>
    <?php endif; ?>

    <div class="box">
        <div class="box-body">

            <div class="row">
                <div class="col-sm-6">
                    <?= $form->field($model, 'name')->input('text') ?>

                    <?= $form->field($model, 'source_id')->dropDownList(Source::getEnumList(),[
                        'prompt' => 'Нет'
                    ]) ?>
                    <div class="form-group">
                        <label><?=$model->getAttributeLabel('competitor') ?></label>
                        <?= FormBuilder::relation($this, $model, 'competitor') ?>
                    </div>
                    <?= $form->field($model, 'used_by_calc')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>
                    <?= $form->field($model, 'matching_api_enabled')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>
                    <?= $form->field($model, 'check_unique_name')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>



                    <div class="form-group">
                        <?= $form->field($model, 'droid_type')->dropDownList([
                                'p-droid' => 'P-Droid - обычный дроид',
                                'v-droid' => 'V-Droid - шайтан-дроид',
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'browser')->dropDownList([null => 'Без браузера','chrome' => 'Chromium','firefox' => 'FireFox']) ?>
                    </div>



                    <div class="form-group">
                        <?= $form->field($model, 'parsing_type')->dropDownList(['normal' => 'Обычный','collecting' => 'Сбор номенклатуры','matching' => 'Сопоставление по Яндексу']) ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'parallel_droids')->input('number') ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'restart_browser')->input('number') ?>
                    </div>

                    <div class="form-group">
                        <label><a href="<?=Url::to(['/masks/view','id' => $model->getParsingProjectMasks()->select('masks_id')->scalar()])?>">Маски</a></label>
                        <?=FormBuilder::renderSelect2($this, Masks::className(), 'ParsingProjectMasks', $model->getParsingProjectMasks()->select('masks_id')->column(), true, 0) ?>

                    </div>
                    <hr/>

                    <?= $form->field($model, 'max_connections')->input('number',['placeholder' => $anyRobot?$anyRobot->max_connections:null]) ?>
                    <?= $form->field($model, 'rate_limit')->input('number',['placeholder' => $anyRobot?$anyRobot->rate_limit:null]) ?>
                    <?= $form->field($model, 'timeout')->input('number',['placeholder' => $anyRobot?$anyRobot->timeout:null]) ?>
                    <?= $form->field($model, 'retries')->input('number',['placeholder' => $anyRobot?$anyRobot->retries:null]) ?>
                    <?= $form->field($model, 'retry_timeout')->input('number',['placeholder' => $anyRobot?$anyRobot->retry_timeout:null]) ?>

                    <?= $form->field($model, 'blocked_domains')->textarea([
                        'class' => 'form-control',
                        'rows'  => 10,
                    ]) ?>

                    <?= $form->field($model, 'urls')->textarea([
                        'class' => 'form-control',
                        'rows'  => 10,
                    ]) ?>

                    <?= $form->field($model, 'comment')->textarea([
                        'class' => 'form-control',
                        'rows'  => 10,
                    ]) ?>

                    <?= $form->field($model, 'signals_enabled')->checkbox() ?>
                    <?= $form->field($model, 'items_per_hour_available')->input('number') ?>
                    <?= $form->field($model, 'errors_per_hour_available')->input('number') ?>

                </div>
                <div class="col-sm-6">

                    <?= $form->field($model, 'vpn_type')->dropDownList(\app\models\reference\ParsingProject::getVpnTypes()) ?>
                    <div class="form-group">
                        <?= Html::activeLabel($model, 'vpns') ?>
                        <?= \app\widgets\FormBuilder::renderSelect2(
                            $this,
                            \app\models\reference\Vpn::className(),
                            \yii\helpers\Html::getInputName($model, 'vpns'),
                            $model->vpns,
                            true,
                            0
                        ); ?>
                    </div>

                    <?= $form->field($model, 'cookies')->textarea([
                        'class' => 'form-control',
                        'rows' => '4'
                    ]) ?>
                    <?= $form->field($model, 'cookies_domain')->input('text') ?>
                    <?= $form->field($model, 'save_browser_cookies')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>
                    <?= Html::beginTag('div',['class' => 'row clearfix']); ?>
                    <?= Html::beginTag('div',['class' => 'col-xs-6']); ?>
                    <?= $form->field($model, 'url_replace_from')->input('text') ?>
                    <?= Html::endTag('div')?>
                    <?= Html::beginTag('div',['class' => 'col-xs-6']); ?>
                    <?= $form->field($model, 'url_replace_to')->input('text') ?>
                    <?= Html::endTag('div')?>
                    <?= Html::endTag('div')?>
                    <?= $form->field($model, 'proxies')->textarea() ?>
                    <?= $form->field($model, 'proxy_bantime')->input('number') ?>
                    <?= $form->field($model, 'ping_url')->input('text') ?>
                    <?= $form->field($model, 'is_our_regions')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>
                    <?= $form->field($model, 'tor_enabled')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>
                    <?= $form->field($model, 'disable_images')->checkbox([
                        'class' => 'checkbox',
                    ]) ?>

                    <?=ListWidget::widget([
                        'title'         => 'Cookies для регионов',
                        'items'         => $model->getParsingProjectRegions()
                            ->orderBy([
                                'sort' => SORT_ASC
                            ])
                            ->all(),
                        'attribute'     => 'region',
                        'relation'      => true,
                        'modelClass'    => \app\models\cross\ParsingProjectRegion::className(),
                        'addOnParam'    => $model,
                        'sortable'      => true,
                        'addOn'         => function($item, $inputName, $index, $model){

                            /** @var ParsingProjectRegion $item */
                            $title  = $item ? $item->region : '__name__';
                            $id     = $item ? 'parsing-project-region-modal-'.$item->region_id : 'parsing-project-region-modal-__id__';
                            $tip    = 'Вставьте сюда Cookies для региона "'.$title.'"';

                            Modal::begin([
                                'id'        => $id,
                                'header'    => '<h2>Cookies для "'.$title.'"</h2>',
                                'footer'    => Html::button('OK', [
                                    'data' => [
                                        'dismiss' => 'modal',
                                        'target'  => '#'.$id,
                                    ],
                                    'class' => 'btn btn-success'
                                ]),
                                'options' => [
                                    'class' => 'parsing-project-region-cookies-modal',
                                    'style' => 'text-align: left;',
                                ]
                            ]);

                            if ($item) {
                                echo Html::textarea($inputName . '[' . $index . '][cookies]', $item->cookies, [
                                    'class'       => 'form-control parsing-project-region-cookies-input',
                                    'placeholder' => $tip,
                                    'rows' => '8'
                                ]);
                                echo Html::beginTag('div',['class' => 'row clearfix']);
                                echo Html::beginTag('div',['class' => 'col-xs-6']);
                                echo Html::input('text', $inputName . '[' . $index . '][url_replace_from]',$item->url_replace_from, [
                                    'class' => 'form-control',
                                    'placeholder' => 'Что заменить в URL',
                                ]);
                                echo Html::endTag('div');
                                echo Html::beginTag('div',['class' => 'col-xs-6']);
                                echo Html::input('text', $inputName . '[' . $index . '][url_replace_to]',$item->url_replace_to, [
                                    'class' => 'form-control  col-xs-6',
                                    'placeholder' => 'На что заменить в URL',
                                ]);
                                echo Html::endTag('div');
                                echo Html::endTag('div');
                            } else {
                                echo Html::textarea('', '', [
                                    'data-name' => $inputName . '[' . $index . '][cookies]',
                                    'class' => 'form-control parsing-project-region-cookies-input',
                                    'placeholder' => $tip,
                                    'rows' => '8'
                                ]);
                                echo Html::beginTag('div',['class' => 'row clearfix']);
                                    echo Html::beginTag('div',['class' => 'col-xs-6']);
                                        echo Html::input('text', '','', [
                                            'data-name' => $inputName . '[' . $index . '][url_replace_from]',
                                            'class' => 'form-control col-xs-6',
                                            'placeholder' => 'Что заменить в URL',
                                        ]);
                                    echo Html::endTag('div');
                                    echo Html::beginTag('div',['class' => 'col-xs-6']);
                                        echo Html::input('text', '','', [
                                            'data-name' => $inputName . '[' . $index . '][url_replace_to]',
                                            'class' => 'form-control  col-xs-6',
                                            'placeholder' => 'На что заменить в URL',
                                        ]);
                                    echo Html::endTag('div');
                                echo Html::endTag('div');
                            }

                            Modal::end();

                            $activeClass = ($item && $item->cookies) ? 'btn-warning' : 'btn-default';

                            echo Html::button('<i class="fa fa-check on-warning" ></i> Cookies', [
                                'onclick' =>  new \yii\web\JsExpression("$('#$id').modal(); return false;"),
                                'class' => "btn $activeClass btn-xs parsing-project-region-cookies"
                            ]);
                            echo " ";
                            echo Html::a('<i class="fa fa-play" ></i> Собрать', $item ? [
                                '/parsing-project/execute',
                                'id'        => $model->id,
                                'regions'   => $item->region_id
                            ] : null, [
                                'onclick' => new \yii\web\JsExpression("window.location.href = this.href; return false;"),
                                'class'     => "btn btn-primary btn-xs",
                                'disabled'  => $item == null
                            ]);
                            echo " ";
                        }
                    ])?>

                    <?= $form->field($model, 'user_agents')->textarea() ?>
                </div>
            </div>

            <?= $model->isNewRecord ? null : Html::a(
                '<span class="fa fa-play"></span> Тест',
                [
                    '/parsing-project/execute',
                    'id'        => $model->id,
                    'test'      => true,
                ],
                ['class' => 'btn btn-primary']
            ) ?>
        </div>
    </div>


<?php
$this->registerJs("$(document).on('hidden.bs.modal','.parsing-project-region-cookies-modal',function(e){
var button = jQuery(this).parents('td').find('.parsing-project-region-cookies');
var input = jQuery(this).find('.parsing-project-region-cookies-input');
 button.removeClass('btn-warning').removeClass('btn-default');
if (input.val()) {
    button.addClass('btn-warning');
} else {
    button.addClass('btn-default');
}
})");
$form->end();
?>