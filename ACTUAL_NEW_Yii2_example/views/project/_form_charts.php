<?php

use app\models\cross\CategoryItem;
use app\models\enum\HoradricCubeStatus;
use app\models\reference\Category;
use app\models\reference\Project;
use app\models\register\HoradricCube;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;
use yii\helpers\Url;

/**
 * @var ActiveForm $form
 * @var Project $model
 * @var array $projectExecutionList
 * @var string $projectExecutionId
 * @var \yii\web\View $this
 */

$modelId = $model->id;
$competitorId = Yii::$app->request->get('competitor_id');
$brandId      = Yii::$app->request->get('brand_id');
$categoryId   = Yii::$app->request->get('category_id');
$itemId       = Yii::$app->request->get('item_id');
$dateInterval = Yii::$app->request->get('dateInterval');

/*$categories = Yii::$app->cache->get('project_' . $model->id . '_categories');

if (!$categories) {
    $categories = CategoryItem::find()
        ->alias('ci')
        ->andWhere([
            'ci.item_id' => $model->getProjectItems()
                ->select('item_id')
                ->groupBy('item_id'),
            'c.is_top' => true,
        ])
        ->innerJoin(['c' => Category::tableName()], 'c.id = ci.category_id')
        ->orderBy(['c.name' => SORT_ASC])
        ->select(['c.id','text' => 'c.name'])
        ->groupBy(['c.id'])
        ->asArray()
        ->all();

    Yii::$app->cache->set('project_' . $model->id . '_categories', $categories, 180);
}*/


?>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs chart-links">
            <li class="chart-link"><a href="<?=Url::to(
                    [
                        '/project-chart', 'iframe' => 1,
                        'ProjectChart[project_id]' => $modelId,
                        'ProjectChart[project_execution_id]' => $projectExecutionId,
                        'ProjectChart[type]' => \app\models\pool\ProjectChart::TYPE_VI_COMPARE,
                        'competitor_id' => $competitorId,
                        'brand_id' => $brandId,
                    ]
                )?>" type="button" class="" target="charts-iframe" data-type="<?= \app\models\pool\ProjectChart::TYPE_VI_COMPARE?>">Сравнение с ценами ВИ</a></li>
            <li class="chart-link"><a href="<?=Url::to(
                    [
                        '/project-chart', 'iframe' => 1,
                        'ProjectChart[project_id]' => $modelId,
                        'ProjectChart[type]' => \app\models\pool\ProjectChart::TYPE_PRICE_DYNAMICS,
                        'competitor_id' => $competitorId,
                        'brand_id' => $brandId,
                    ]
                )?>" type="button" class="" target="charts-iframe" data-type="<?= \app\models\pool\ProjectChart::TYPE_PRICE_DYNAMICS?>">Динамика цен</a></li>
            <li class="pull-right charts-filters">
                <div class="row">
                    <div class="col-xs-3">
                        <?= \app\widgets\FormBuilder::renderSelect2($this,
                            \app\models\reference\Competitor::className(),
                            'competitor_id',
                            $competitorId,
                            false,
                            0,
                            'Конкурент'
                        ) ?>
                    </div>
                    <div class="col-xs-3">
                        <?= \app\widgets\FormBuilder::renderSelect2($this,
                            \app\models\reference\Brand::className(),
                            'brand_id',
                            $brandId,
                            false,
                            0,
                            'Бренд'
                        ) ?>
                    </div>
                    <div class="col-xs-3">
                        <?= \app\widgets\FormBuilder::renderSelect2($this,
                            \app\models\reference\Item::className(),
                            'item_id',
                            $itemId,
                            false,
                            0,
                            'Товар'
                        ) ?>
<!--                        < ?//= \maddoger\widgets\Select2::widget([
//                            'name'          => 'category_id',
//                            'clientOptions' => [
//                                'width'             => '100%',
//                                'data'              => $categories,
//                                'allowClear'        => true,
//                            ],
//                            'options' => [
//                                'id'            => 'category_id',
//                                'class'         => 'select2',
//                                'placeholder'   => 'Категория',
//                            ],
//                        ])
//
//                        ?>-->
                    </div>
                    <div class="col-xs-3">
                        <?= kartik\daterange\DateRangePicker::widget([
                            'name' => 'date_interval',
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'timePicker' => false,
                                'locale' => [
                                    'format' => 'd-m-Y'
                                ]
                            ],
                        ]) ?>
                    </div>
                </div>
            </li>
        </ul>
        <div style="height: 4px;"></div>
        <iframe id="charts-iframe" name="charts-iframe" src="" style="border: none; width: 100%; height: 100px; overflow: scroll;" onload=""></iframe>
    </div>
<?php

$this->registerJs(<<< JS
function iframeFit() {
    var ifr = jQuery('#charts-iframe');
    ifr.css('height', jQuery(window).height() - 80 + jQuery(window).scrollTop() - ifr.offset().top);
}
iframeFit();
jQuery(window).resize(iframeFit);
jQuery(window).scroll(iframeFit);

$('.chart-link a').on('click', function () {
    $('.chart-link a').parent().removeClass('active');
    $(this).parent().addClass('active');
});
$('.charts-filters input.select2, .charts-filters select, .charts-filters input[name="date_interval"]').on('change', function() {
    let url = '/project-chart?iframe=1'
            + '&ProjectChart[project_id]={$modelId}'
            + '&competitor_id=' + $('input[name="competitor_id"]').val()
            //+ '&category_id='   + $('input[name="category_id"]').val()
            + '&date_interval=' + $('input[name="date_interval"]').val()
            + '&item_id=' + $('input[name="item_id"]').val()
            + '&brand_id='      + $('input[name="brand_id"]').val();
    $('.chart-link a').each(function(i, el) {
        $(el).attr('href', url + '&ProjectChart[type]=' + $(el).attr('data-type'));
    });
    $('.chart-link.active a')[0]?.click();
});

JS
);