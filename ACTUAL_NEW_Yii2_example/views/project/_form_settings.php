<?php
use app\models\enum\PriceExportMode;
use app\models\enum\Source;
use app\models\reference\Project;
use app\widgets\FormBuilder;
use kartik\time\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/**
 * @var ActiveForm $form
 * @var Project $model
 */

?>
<div class="panel">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-7"><?= $form->field($model, 'name')->input('text') ?></div>
            <div class="col-sm-5">
                <label><?=$model->getAttributeLabel('projectTheme')?></label>
                <?= FormBuilder::relation($this, $model, 'projectTheme') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <label>Регионы</label>
                <?=FormBuilder::renderSelect2($this, \app\models\enum\Region::className(), 'Regions', $model->getProjectRegionIds(), true, 0) ?>
            </div>
            <div class="col-sm-3">
                <label>Торговые площадки</label>
                <div class="checkbox">
                    <?=Html::checkboxList('ProjectSources[]',$model->getProjectSourceIds(), Source::getEnumList())?>
                </div>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'supply_price_threshold',['addon' => ['append' => ['content'=>'₽']]])
                    ->input('number', ['step' => 50,'style'=>'text-align:right;'])?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'use_vi')->checkbox(['class' => 'checkbox'])?>
            </div>
        </div>

        <legend>Расчет цен</legend>
        <div class="row">
            <div class="col-sm-4"><?= $form->field($model, 'min_margin',[
                    'addon' => ['append' => ['content'=>'%']]
                ])->input('number',['step' => 0.1,'style'=>'text-align:right;'])?></div>
            <div class="col-sm-4"><?= $form->field($model, 'priceRelevanceTimeSpan')->input('text') ?></div>
            <div class="col-sm-4"><?= $form->field($model, 'dataLifeTimeSpan')->input('text') ?></div>
            <div class="col-sm-4"><?= $form->field($model, 'price_filter_type')->dropDownList(Project::getPriceFilterTypes()); ?></div>
        </div>

        <legend>Запуск</legend>
        <div class="row">
            <div class="col-sm-4">
                <label>
                    <input type="checkbox" id="scheduled_daily" <?=$model->scheduled_daily_time?"checked":""?>> <?=$model->getAttributeLabel('scheduled_daily_time')?>
                </label>
                <div class="input-group">
                    <?= TimePicker::widget([
                        'name'      => 'Project[scheduled_daily_time]',
                        'value'     => $model->scheduled_daily_time,
                        'disabled'  => !$model->scheduled_daily_time,
                        'options'   => [
                            'id' => 'scheduled_daily_time',
                        ],
                        'pluginOptions' => [
                            'showSeconds'  => false,
                            'showMeridian' => false,
                            'defaultTime'  => false,
                        ]
                    ]);?>
                </div>
                <?= Html::checkboxList('Project[scheduledWeekdays][]', $model->scheduledWeekdays, [
                    1 => 'Пн',
                    2 => 'Вт',
                    3 => 'Ср',
                    4 => 'Чт',
                    5 => 'Пт',
                    6 => 'Сб',
                    7 => 'Вс',
                ], [
                    'class' => 'form-inline checkbox'
                ]) ?>
            </div>
            <div class="col-sm-4">
                <label style="margin: -10px 0 0 0;"><?= $form->field($model, 'is_logging')->checkbox() ?></label>
            </div>
        </div>

        <legend>Выгрузка цен</legend>
        <div class="row">
            <div class="col-sm-4">
                <label>Типы цен Прайсформера</label>
                <?=FormBuilder::renderSelect2($this, \app\models\reference\PriceFormerType::className(), 'ProjectPriceFormerTypes', $model->getPriceFormerTypeIds(), true, 0) ?>
            </div>
            <div class="col-sm-4"><?= $form->field($model, 'price_export_mode_id')->dropDownList(PriceExportMode::getEnumList()); ?></div>
            <div class="col-sm-4"><?= $form->field($model, 'is_auto_export')->checkbox() ?></div>
        </div>

        <legend> Фильтрация цен</legend>
        <div class="row">
            <div class="col-sm-2"><?= $form->field($model, 'price_range_k1')->input('number', ['step' => 0.1])?></div>
            <div class="col-sm-2"><?= $form->field($model, 'price_range_k2')->input('number', ['step' => 0.1])?></div>
            <div class="col-sm-4"><?= $form->field($model, 'price_range_threshold')->input('number', ['step' => 100])?></div>
            <div class="col-sm-2"><?= $form->field($model, 'price_range_k3')->input('number', ['step' => 0.1])?></div>
            <div class="col-sm-2"><?= $form->field($model, 'price_range_k4')->input('number', ['step' => 0.1])?></div>
        </div>
        <div class="alert alert-info">
            <p>
                <strong><span class="glyphicon glyphicon-info-sign"></span> Формула фильтрации </strong>
            </p>

            <p>
                Выше пороговой суммы работает формула:<br/>
                (K1 &times; <strong>Y</strong>) &le; <strong>X</strong> &le; (K2 &times; <strong>Z</strong>),
            </p>

            <p>
                Ниже пороговой суммы работает формула:<br/>
                (K3 &times; <strong>Y</strong>) &le; <strong>X</strong> &le; (K4 &times; <strong>Z</strong>), где
            </p>

            <p>
                <strong>X</strong> - искомая цена на товар у конкурента<br/>
                <strong>Y</strong> - закупочная цена на товар<br/>
                <strong>Z</strong> - Продажная ВИ МСК
            </p>
        </div>

    </div>
</div>
